<?php
// Shared table-fragment renderer for all Reports modules.
// Expects: $columns = [['key'=>'trip_code','label'=>'Trip Code','align'=>'left','format'=>'text'], ...]
//          $rows    = [ ['trip_code'=>'TRP-2026-000001', ...], ... ]
// format: text | number | currency | date | badge
// A column may set 'badge_map' => ['DELIVERED' => 'badge-success', ...] used only when format = badge.

function __reportCellValue($col, $row)
{
    $val = $row[$col['key']] ?? null;
    $format = $col['format'] ?? 'text';

    if ($val === null || $val === '') {
        return '—';
    }

    switch ($format) {
        case 'currency':
            return '&#8369;' . number_format((float) $val, 2);
        case 'number':
            return number_format((float) $val, 2);
        case 'date':
            $ts = strtotime($val);
            return $ts ? date('M d, Y', $ts) : '—';
        case 'badge':
            $map = $col['badge_map'] ?? [];
            $cls = $map[$val] ?? 'badge-secondary';
            return '<span class="badge ' . $cls . '">' . esc(str_replace('_', ' ', $val)) . '</span>';
        default:
            return esc((string) $val);
    }
}
?>
<thead>
    <tr>
        <?php foreach ($columns as $col): ?>
            <th class="<?= ($col['align'] ?? 'left') === 'right' ? 'text-end' : (($col['align'] ?? 'left') === 'center' ? 'text-center' : ''); ?>">
                <?= esc($col['label']); ?>
            </th>
        <?php endforeach; ?>
    </tr>
</thead>
<tbody>
    <?php if (!empty($rows)): ?>
        <?php foreach ($rows as $row): ?>
            <tr>
                <?php foreach ($columns as $col): ?>
                    <td class="<?= ($col['align'] ?? 'left') === 'right' ? 'text-end' : (($col['align'] ?? 'left') === 'center' ? 'text-center' : ''); ?>">
                        <?= __reportCellValue($col, $row); ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</tbody>
