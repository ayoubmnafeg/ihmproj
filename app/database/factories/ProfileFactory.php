<?php

namespace Database\Factories;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Profile>
 */
class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    public function definition(): array
    {
        $adjectives = ['Silent','Brave','Swift','Clever','Bold','Calm','Dark','Epic','Fierce','Gentle','Happy','Iron','Jolly','Kind','Lucky','Mighty','Noble','Odd','Proud','Quick','Rapid','Sharp','Tough','Ultra','Vivid','Wild','Zany','Amber','Blaze','Coral','Dusk','Ember','Frost','Gloom','Haze','Ivory','Jade','Keen','Lime','Mist','Neon','Opal','Ruby','Sage','Teal','Angry','Azure','Bitter','Bright','Bronze','Cloudy','Crazy','Crimson','Cyber','Dizzy','Dusty','Eternal','Faded','Famous','Fancy','Frozen','Funky','Fuzzy','Giant','Gloomy','Golden','Goofy','Grand','Grave','Grim','Grumpy','Hidden','Hollow','Hyper','Icy','Jumpy','Laser','Lazy','Lone','Lush','Magic','Massive','Mega','Messy','Metal','Mystic','Nifty','Nutty','Obsessed','Phantom','Plucky','Polar','Prickly','Psycho','Rogue','Royal','Rusty','Savage','Shadowy','Shiny','Sneaky','Solar','Spicy','Stormy','Striped','Super','Turbo','Twisted','Wicked','Windy','Woolly'];
        $nouns      = ['Fox','Panda','Tiger','Wolf','Eagle','Bear','Lion','Shark','Hawk','Deer','Lynx','Crow','Mole','Newt','Owl','Pike','Quail','Raven','Seal','Toad','Viper','Wasp','Yak','Bison','Cobra','Drake','Finch','Goat','Hyena','Ibis','Jackal','Kite','Llama','Moose','Narwhal','Otter','Parrot','Rhino','Sloth','Tapir','Urial','Vole','Walrus','Xerus','Zebra','Ant','Bat','Bug','Cat','Dog','Elk','Emu','Gnu','Hog','Jay','Ram','Rat','Roe','Slug','Snail','Swan','Wren','Crab','Clam','Frog','Gull','Lark','Mink','Moth','Mule','Pony','Pup','Stag','Trout','Tuna','Worm','Colt','Dove','Duck','Fawn','Fish','Gnat','Hare','Koi','Lamb','Loon','Mare','Mite','Oxen','Pika','Puma','Shrew','Skunk','Snipe','Stoat','Swift','Tern','Vixen'];

        return [
            'display_name' => fake()->unique()->randomElement($adjectives) . fake()->randomElement($nouns) . rand(100, 999),
            'gender'       => fake()->optional(0.8)->randomElement(['male', 'female']),
        ];
    }
}
