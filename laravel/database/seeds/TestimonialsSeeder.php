<?php

use Illuminate\Database\Seeder;
use App\Models\Testimonial;

class TestimonialsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Testimonial::unguard();
        Testimonial::create([
            'text' => 'Signals365.com is my favourite signals provider. They offer more profitable signals than any other competitors and the support is great and transparent about their services. Also you can see all past results verified on their website once you login.',
            'author' => 'Alan Simons',
            'image' =>'testimonial-1.png'
        ]);
        Testimonial::create([
            'text' => 'I\'ve tried using binary options robots but lost most of my money. These guys are for real and provide quite good results.',
            'author' => 'David Goldstein',
            'image' =>'testimonial-2.png'
        ]);
        Testimonial::create([
            'text' => 'These guys know their stuff and provide very good, reliable trading signals. They also taught me how to manage my money and not over trade.',
            'author' => 'Akpo Khan',
            'image' =>'testimonial-3.png'
        ]);
        Testimonial::create([
            'text' => 'Managed to triple my broker account with this service and their trading team provided a lot of helpful advice when I asked about their strategies.',
            'author' => 'Ted Gorodetzky',
            'image' =>'testimonial-4.png'
        ]);
        Testimonial::reguard();
    }
}