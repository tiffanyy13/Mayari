<?php
namespace Database\Seeders;
 
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
 
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'firstName' => 'Admin',
            'lastName'  => 'Mayari',
            'phone'     => '09000000000',
            'email'     => 'admin@mayari.com',
            'password'  => Hash::make('mayari1234'),
            'role'      => 'admin',
        ]);
 
        User::create([
            'firstName' => 'Tiffany',
            'lastName'  => 'Yap',
            'phone'     => '09088881306',
            'email'     => 'tiffany@mayari.com',
            'password'  => Hash::make('mayari1113'),
            'role'      => 'customer',
        ]);
 
        $face = Category::create(['catName' => 'Face']);
        $eye  = Category::create(['catName' => 'Eye']);
        $lip  = Category::create(['catName' => 'Lip']);
 
        $faceItems = [
            ['pName' => 'Cream Blush',       'descript' => 'Cream Blush, Long wear, Face Makeup', 'price' => 13.99, 'stock' => 13],
            ['pName' => 'Liquid Foundation', 'descript' => 'Lightweight, buildable coverage',      'price' => 18.99, 'stock' => 20],
            ['pName' => 'Setting Powder',    'descript' => 'Finely milled, matte finish',          'price' => 12.50, 'stock' => 15],
            ['pName' => 'Highlighter',       'descript' => 'Champagne shimmer, buildable glow',    'price' => 15.99, 'stock' => 10],
            ['pName' => 'Contour Stick',     'descript' => 'Sculpt and define, easy blend',        'price' => 11.99, 'stock' => 18],
            ['pName' => 'BB Cream',          'descript' => 'SPF 30, natural finish, skin care',    'price' => 16.00, 'stock' => 25],
        ];
        foreach ($faceItems as $p) {
            Product::create(array_merge($p, ['categoryID' => $face->categoryID, 'image' => 'example.image']));
        }
 
        $eyeItems = [
            ['pName' => 'Eyeshadow Palette', 'descript' => '12-pan neutral palette, long wear', 'price' => 24.99, 'stock' =>  8],
            ['pName' => 'Mascara',           'descript' => 'Volumizing & lengthening formula',  'price' =>  9.99, 'stock' => 30],
            ['pName' => 'Eyeliner Pencil',   'descript' => 'Waterproof, smudge-proof formula',  'price' =>  7.50, 'stock' => 22],
            ['pName' => 'Brow Gel',          'descript' => 'Tinted, flexible hold brow gel',    'price' =>  8.99, 'stock' => 17],
        ];
        foreach ($eyeItems as $p) {
            Product::create(array_merge($p, ['categoryID' => $eye->categoryID, 'image' => 'example.image']));
        }
 
        $lipItems = [
            ['pName' => 'Matte Lipstick', 'descript' => 'Rich pigment, 8-hr wear',         'price' => 10.99, 'stock' => 20],
            ['pName' => 'Lip Gloss',      'descript' => 'High shine, non-sticky formula',  'price' =>  8.50, 'stock' => 25],
            ['pName' => 'Lip Liner',      'descript' => 'Precise tip, long-lasting color', 'price' =>  6.99, 'stock' => 18],
            ['pName' => 'Lip Tint',       'descript' => 'Sheer wash of color, hydrating',  'price' =>  9.50, 'stock' => 12],
        ];
        foreach ($lipItems as $p) {
            Product::create(array_merge($p, ['categoryID' => $lip->categoryID, 'image' => 'example.image']));
        }
    }
}
