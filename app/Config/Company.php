<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Company details printed on official documents (sales invoice).
 * Fill in the blanks — an empty value prints as a blank line to be filled in by hand.
 */
class Company extends BaseConfig
{
    public string $name    = 'LARGA FLEET MANAGEMENT';
    public string $tagline = 'Trucking & Logistics Services';
    public string $address = '';
    public string $tin     = '';            // VAT Reg. TIN, e.g. 000-000-000-00000
    public string $phone   = '(02) 1234-5678';
    public string $email   = 'info@largafleet.com';
    public float  $vatRate = 0.12;
}
