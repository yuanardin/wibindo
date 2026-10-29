<?php
declare(strict_types=1);

enum Category: string
{
    case Warehouse  = 'Gudang & Logistik';
    case Production = 'Produksi';
    case Assets     = 'Aset & Inventaris';
    case Media      = 'Infrastruktur & Media';
    case Outlet     = 'Operasional Outlet';
}
