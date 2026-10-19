<?php
namespace App\Models;
use CodeIgniter\Model;

// ==============================
// ERP BOOKING SLIPS (erplogisticdev) — READ ONLY
// Trip Scheduling looks up booking slips that have a Trucking line and turns them into trips.
// ==============================
class FMS_ErpBooking_Model extends Model
{
    protected $db;
    protected $erp;

    // mst_trade_service_charges_type.id for "Trucking"
    const TRUCKING_TSC_ID = 6;
    // Only booking slips the ERP has approved (status BOOKED) can become trips
    const BOOKED_STATUS = 'BOOKED';

    public function __construct(){
        parent::__construct();
        $this->session = session();
        $this->request = \Config\Services::request();
        $this->db = \Config\Database::connect();
        $this->erp = \Config\Database::connect('erp');
        $this->cuser = $this->session->get('__xsys_myuserzicas__');
    }

    // ERP stores some text with escaped quotes (e.g. LARRA\'S)
    private function clean($v)
    {
        return trim(stripslashes((string) $v));
    }

    // ==============================
    // SEARCH BOOKINGS WITH TRUCKING (by booking no., client, client ref, shipper)
    // ==============================
    public function searchTruckingBookings($term)
    {
        $like = '%' . trim((string) $term) . '%';
        $rows = $this->erp->query("
            SELECT h.id, h.sysctrlno, h.client_ref_no, h.client, h.shipper, h.status, h.muser_apprvd, h.date_proc,
                   sv.service_desc mode_of_shipment
            FROM trx_bookslip_hd h
            LEFT JOIN mst_services sv ON sv.recid = h.type_of_service_id
            WHERE h.status = ?
              AND EXISTS (SELECT 1 FROM trx_bookslip_dt d WHERE d.hd_id = h.id AND d.tsc_id = ?)
              AND (h.sysctrlno LIKE ? OR h.client LIKE ? OR h.client_ref_no LIKE ? OR h.shipper LIKE ?)
            ORDER BY h.date_proc DESC
            LIMIT 20
        ", [self::BOOKED_STATUS, self::TRUCKING_TSC_ID, $like, $like, $like, $like])->getResultArray();

        $trips = $this->tripsPerBooking(array_column($rows, 'id'));
        foreach ($rows as &$r) {
            $r['client'] = $this->clean($r['client']);
            $r['shipper'] = $this->clean($r['shipper']);
            $r['approved'] = trim((string) $r['muser_apprvd']) !== '';
            $r['trip_count'] = $trips[$r['id']] ?? 0;
        }
        return $rows;
    }

    // A trip can only be linked to a BOOKED slip that has trucking
    public function isBookedTrucking($bookslip_id)
    {
        return (bool) $this->erp->query("
            SELECT 1 FROM trx_bookslip_hd h
            WHERE h.id = ? AND h.status = ?
              AND EXISTS (SELECT 1 FROM trx_bookslip_dt d WHERE d.hd_id = h.id AND d.tsc_id = ?)
        ", [$bookslip_id, self::BOOKED_STATUS, self::TRUCKING_TSC_ID])->getRow();
    }

    // How many FMS trips already came from each booking
    private function tripsPerBooking(array $ids)
    {
        if (!$ids) return [];
        $rows = $this->db->query("
            SELECT erp_bookslip_id, COUNT(*) as total FROM tbl_trips
            WHERE erp_bookslip_id IN (" . implode(',', array_map('intval', $ids)) . ") AND trip_status <> 'CANCELLED'
            GROUP BY erp_bookslip_id
        ")->getResultArray();
        return array_column($rows, 'total', 'erp_bookslip_id');
    }

    // ==============================
    // GET ONE BOOKING + WHAT IT MEANS FOR A TRIP
    // ==============================
    public function getTruckingBooking($bookslip_id)
    {
        $h = $this->erp->query("
            SELECT aa.*, sv.service_desc, nc.incot_code, nc.incot_name, bb.name commodity_name, cc.TYPE_DESC commodity_type_desc
            FROM trx_bookslip_hd aa
            LEFT JOIN mst_services sv ON sv.recid = aa.type_of_service_id
            LEFT JOIN mst_commodity bb ON bb.recid = aa.commodity_id
            LEFT JOIN mst_commodity_type cc ON cc.recid = aa.commodity_type_id
            LEFT JOIN mst_inco_terms nc ON nc.id = aa.incot_id
            WHERE aa.id = ?
        ", [$bookslip_id])->getRowArray();
        if (!$h) return null;

        $charges = $this->getTruckingCharges($h);
        if (!$charges) return null;   // not a trucking booking

        // ---- cargo ----
        $commodity = $this->clean($h['commodity_name'] ?: $h['commodity']);
        $cargo_items = [];
        $quantity = 0; $unit = ''; $weight = 0;
        $fcl = $this->erp->query("
            SELECT f.fcl_no_units, ct.description size, cn.COM_DESC container
            FROM trx_bookslip_cargo_fcl f
            LEFT JOIN mst_cargo_container_type ct ON ct.recid = f.fcl_cs
            LEFT JOIN mst_cargo_container cn ON cn.recid = f.fcl_cn
            WHERE f.hd_id = ?
        ", [$bookslip_id])->getResultArray();
        $lcl = $this->erp->query("SELECT * FROM trx_bookslip_cargo_lcl WHERE hd_id = ?", [$bookslip_id])->getResultArray();
        foreach ($fcl as $c) {
            $n = (float) $c['fcl_no_units'];
            $quantity += $n;
            $cargo_items[] = ['item_description' => trim($c['size'] . ' ' . $c['container']) . ' — ' . $commodity, 'quantity' => $n, 'unit' => 'Container', 'weight' => 0, 'remarks' => ''];
        }
        if ($fcl) $unit = 'Container';
        foreach ($lcl as $c) {
            // ERP column names are swapped: lcl_weight = unit text ("KG"), lcl_weight_unit = the number
            $n = (float) $c['lcl_no_units'];
            $w = (float) $c['lcl_weight_unit'];
            $dims = (float) $c['lcl_dim_length'] . ' x ' . (float) $c['lcl_dim_width'] . ' x ' . (float) $c['lcl_dim_height'] . ' ' . $c['lcl_dimension'];
            $quantity += $n;
            $weight += strtoupper(trim($c['lcl_weight'])) === 'KG' ? $w : 0;
            $cargo_items[] = ['item_description' => $commodity . ' (' . $dims . ')', 'quantity' => $n, 'unit' => 'Units', 'weight' => $w, 'remarks' => trim($w . ' ' . $c['lcl_weight'])];
        }
        if ($lcl && !$fcl) $unit = 'Units';

        // ---- route: the truck leg of the shipment ----
        $mode = strtoupper((string) $h['service_desc']);
        $client_addr = $this->clean($h['client_addr']);
        // ERP stores "[PH MNL] MANILA" + "[PH] Philippines" → "Port of MANILA (PH MNL), Philippines"
        $port = function($country, $portName) {
            $p = $this->clean($portName);
            $c = trim(preg_replace('/^\[[^\]]*\]\s*/', '', $this->clean($country)));
            if (preg_match('/^\[([^\]]+)\]\s*(.+)$/', $p, $m)) $p = 'Port of ' . $m[2] . ' (' . $m[1] . ')';
            elseif ($p !== '') $p = 'Port of ' . $p;
            return trim($p . ($c !== '' ? ', ' . $c : ''), ' ,');
        };
        if (str_contains($mode, 'IMPORT')) {
            $origin = $port($h['country_of_destination'], $h['port_of_destination']);   // arrival port → client
            $destination = $client_addr;
        } elseif (str_contains($mode, 'EXPORT')) {
            $origin = $client_addr;                                                       // client → departure port
            $destination = $port($h['origin_country'], $h['port_of_origin']);
        } else {
            $origin = $port($h['origin_country'], $h['port_of_origin']);
            $destination = $client_addr;
        }

        // ---- notes ----
        $commodity_type = $this->clean($h['commodity_type_desc']);
        $instructions = [];
        if (stripos($commodity_type, 'Hazardous') !== false && stripos($commodity_type, 'Non') === false) {
            $instructions[] = 'HAZARDOUS CARGO — handle per dangerous goods rules.';
        }
        if (trim((string) $h['remarks']) !== '') $instructions[] = $this->clean($h['remarks']);

        $trip_count = $this->tripsPerBooking([$bookslip_id])[$bookslip_id] ?? 0;

        return [
            'erp_bookslip_id'  => (int) $h['id'],
            'booking_reference' => $h['sysctrlno'],
            'status'           => $h['status'],
            'approved'         => trim((string) $h['muser_apprvd']) !== '',
            'approved_by'      => trim((string) $h['muser_apprvd']),
            'approved_at'      => $h['mdate_apprvd'],
            'date_validity'    => substr((string) $h['date_validity'], 0, 10),
            'client_reference' => $this->clean($h['client_ref_no']),
            'client'           => $this->clean($h['client']),
            'client_addr'      => $client_addr,
            'shipper_name'     => $this->clean($h['shipper']),
            'mode_of_shipment' => $h['service_desc'],
            'incoterms'        => $h['incot_code'] ? '[' . $h['incot_code'] . '] ' . $h['incot_name'] : null,
            'commodity'        => $commodity,
            'commodity_type'   => $commodity_type,
            'agent'            => $this->clean($h['agent']),
            'origin'           => $origin,
            'destination'      => $destination,
            'cargo_description' => trim($commodity . ($commodity_type ? ' (' . $commodity_type . ')' : '')),
            'quantity'         => $quantity,
            'unit'             => $unit,
            'estimated_weight' => $weight,
            'special_instructions' => implode("\n", $instructions),
            'cargo_items'      => $cargo_items,
            'charges'          => $charges,
            'charges_total_php' => round(array_sum(array_map(fn($c) => $c['selling_php'] * $c['quantity'], $charges)), 2),
            'trip_count'       => $trip_count,
            'customer'         => $this->findCustomer($h['orgzn_client_id'], $this->clean($h['client'])),
        ];
    }

    // Trucking lines of a booking, with selling converted to PHP the same way the booking slip PDF does.
    // `selling` is net of VAT (the ERP keeps VAT apart in charge_vat_amt) — ERP VAT is never carried over,
    // FMS billing adds the 12% VAT once on top of this net rate.
    private function getTruckingCharges(array $h)
    {
        $rows = $this->erp->query("
            SELECT d.id, d.qty, d.cost, d.selling, d.rate_validity,
                   itm.tsc_desc description, b.basis_desc, c.CURR_CODE currency, org.orgzn_name payment_to
            FROM trx_bookslip_dt d
            LEFT JOIN mst_trade_service_charges itm ON itm.id = d.item_id
            LEFT JOIN mst_basis b ON b.recid = d.charge_basis_id
            LEFT JOIN mst_currency c ON c.recid = d.charge_currency_id
            LEFT JOIN mst_orgzn org ON org.recid = d.tsc_costccomp_id
            WHERE d.hd_id = ? AND d.tsc_id = ?
            ORDER BY d.id
        ", [$h['id'], self::TRUCKING_TSC_ID])->getResultArray();

        $er = (float) $h['er_cost'];
        return array_map(function($r) use ($er) {
            $php = ($r['currency'] !== 'PHP' && $er > 0) ? (float) $r['selling'] * $er : (float) $r['selling'];
            return [
                'erp_bookslip_dt_id' => (int) $r['id'],
                'description'  => $this->clean($r['description']),
                'charge_basis' => $r['basis_desc'],
                'currency'     => $r['currency'] ?: 'PHP',
                'quantity'     => (float) $r['qty'] ?: 1,
                'cost'         => (float) $r['cost'],
                'selling'      => (float) $r['selling'],
                'selling_php'  => round($php, 2),
                'payment_to'   => $this->clean($r['payment_to']),
                'rate_validity' => $r['rate_validity'] && $r['rate_validity'] !== '0000-00-00' ? $r['rate_validity'] : null,
            ];
        }, $rows);
    }

    // ==============================
    // CUSTOMER: linked ERP client → same name → (created on save)
    // ==============================
    private function findCustomer($orgzn_id, $client_name)
    {
        $c = $this->db->query("SELECT customer_id, customer_name FROM tbl_customers WHERE erp_orgzn_id = ?", [$orgzn_id])->getRowArray();
        if (!$c) {
            $c = $this->db->query("SELECT customer_id, customer_name FROM tbl_customers WHERE UPPER(TRIM(customer_name)) = UPPER(TRIM(?)) LIMIT 1", [$client_name])->getRowArray();
        }
        return $c
            ? ['customer_id' => (string) $c['customer_id'], 'customer_name' => $c['customer_name'], 'is_new' => false]
            : ['customer_id' => 'erp:' . (int) $orgzn_id, 'customer_name' => $client_name, 'is_new' => true];
    }

    // Turn "erp:<orgzn_id>" into a real FMS customer_id (link by name, or create from ERP)
    public function resolveCustomer($value)
    {
        if (!preg_match('/^erp:(\d+)$/', (string) $value, $m)) return $value;
        $orgzn_id = (int) $m[1];

        $linked = $this->db->query("SELECT customer_id FROM tbl_customers WHERE erp_orgzn_id = ?", [$orgzn_id])->getRow();
        if ($linked) return $linked->customer_id;

        $org = $this->erp->query("SELECT * FROM mst_orgzn WHERE recid = ?", [$orgzn_id])->getRowArray();
        if (!$org) return null;

        $byName = $this->db->query("SELECT customer_id FROM tbl_customers WHERE UPPER(TRIM(customer_name)) = UPPER(TRIM(?)) AND erp_orgzn_id IS NULL LIMIT 1", [$this->clean($org['ORGZN_NAME'])])->getRow();
        if ($byName) {
            $this->db->query("UPDATE tbl_customers SET erp_orgzn_id = ? WHERE customer_id = ?", [$orgzn_id, $byName->customer_id]);
            return $byName->customer_id;
        }

        $customers = model('App\Models\FMS_Customer_Model');
        $customers->cuser = $this->cuser;
        return $customers->createFromErp($org);
    }

    // ==============================
    // SNAPSHOT A BOOKING ONTO A TRIP (reference fields + trucking quote lines)
    // ==============================
    public function snapshotToTrip($trip_id, $bookslip_id)
    {
        $b = $this->getTruckingBooking($bookslip_id);
        if (!$b) return false;

        $this->db->query("
            UPDATE tbl_trips SET erp_bookslip_id = ?, client_reference = ?, shipper_name = ?, mode_of_shipment = ?, incoterms = ?, commodity_type = ?
            WHERE trip_id = ?
        ", [$b['erp_bookslip_id'], $b['client_reference'], $b['shipper_name'], $b['mode_of_shipment'], $b['incoterms'], $b['commodity_type'], $trip_id]);

        $this->db->query("DELETE FROM tbl_trip_booking_charges WHERE trip_id = ?", [$trip_id]);
        foreach ($b['charges'] as $c) {
            $this->db->query("
                INSERT INTO tbl_trip_booking_charges(
                    trip_id, erp_bookslip_dt_id, description, charge_basis, currency, quantity,
                    cost, selling, selling_php, payment_to, rate_validity, created_by
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ", [
                $trip_id, $c['erp_bookslip_dt_id'], $c['description'], $c['charge_basis'], $c['currency'], $c['quantity'],
                $c['cost'], $c['selling'], $c['selling_php'], $c['payment_to'], $c['rate_validity'], $this->cuser
            ]);
        }
        return true;
    }

    // Trip's saved booking quote (for the trip form and billing prefill)
    public function getTripCharges($trip_id)
    {
        return $this->db->query("SELECT * FROM tbl_trip_booking_charges WHERE trip_id = ? ORDER BY charge_id", [$trip_id])->getResultArray();
    }
}
