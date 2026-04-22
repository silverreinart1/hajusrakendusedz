<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class ShopController extends Controller
{
    public static function products(): array
    {
        return [
            ['id' => 1, 'name' => 'Tallinna Kohv',     'price' => 12.99, 'description' => 'Käsitööna röstitud, intensiivne ja sametine aromaatika.',          'image' => 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=400', 'category' => 'Joogid'],
            ['id' => 2, 'name' => 'Eesti Mesi',         'price' =>  8.50, 'description' => 'Looduslik õunapuu mesi Lääne-Eestist, 500g purk.',                  'image' => 'https://images.unsplash.com/photo-1587049352851-8d4e89133924?w=400', 'category' => 'Toiduained'],
            ['id' => 3, 'name' => 'Kanepi Küünal',      'price' => 18.00, 'description' => 'Sojakütusega aromaatne küünal, 40h põlemisaeg.',                     'image' => 'https://images.unsplash.com/photo-1602607144535-163ee32c9d40?w=400', 'category' => 'Kodu'],
            ['id' => 4, 'name' => 'Lõhnaõli Komplekt',  'price' => 24.99, 'description' => 'Kolm erinevat lõhnaõli kadaka, männi ja merega.',                   'image' => 'https://images.unsplash.com/photo-1571781926291-c477ebfd024b?w=400', 'category' => 'Kodu'],
            ['id' => 5, 'name' => 'Puitmärkmik',        'price' => 15.50, 'description' => 'Käsitsi köidetud A5 märkmik tamme kaanepapiga.',                     'image' => 'https://images.unsplash.com/photo-1531346878377-a5be20888e57?w=400', 'category' => 'Kirjatarbed'],
            ['id' => 6, 'name' => 'Graniit Kruus',      'price' => 22.00, 'description' => 'Käsitsi tehtud 350ml kruus Lõuna-Eesti savist.',                     'image' => 'https://images.unsplash.com/photo-1514228742587-6b1558fcca3d?w=400', 'category' => 'Kodu'],
            ['id' => 7, 'name' => 'Villane Sokid',      'price' =>  9.99, 'description' => 'Meriinovillast soojad sokid Eesti mustriga, suurus 36–46.',           'image' => 'https://images.unsplash.com/photo-1586350977771-b3b0abd50c82?w=400', 'category' => 'Rõivad'],
            ['id' => 8, 'name' => 'Kadakamoos',         'price' =>  6.99, 'description' => 'Metsikute kadakamarjadega moos juustule ja liharoogadele.',           'image' => 'https://images.unsplash.com/photo-1563636619-e9143da7973b?w=400', 'category' => 'Toiduained'],
            ['id' => 9, 'name' => 'Linane Käekott',     'price' => 31.00, 'description' => 'Naturaalsest linasest riidest kott, käsitsi tikitud Eesti mustriga.', 'image' => 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=400', 'category' => 'Aksessuaarid'],
        ];
    }

    public function index()
    {
        return Inertia::render('Shop/Index', [
            'products' => self::products(),
        ]);
    }
}
