<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Daftar Tech Stack Populer & Kode Simple Icons-nya
        $skills = [
            ['name' => 'Laravel', 'identifier' => 'simple-icons:laravel'],
            ['name' => 'Vue.js', 'identifier' => 'simple-icons:vuedotjs'],
            ['name' => 'React', 'identifier' => 'simple-icons:react'],
            ['name' => 'Node.js', 'identifier' => 'simple-icons:nodedotjs'],
            ['name' => 'Tailwind CSS', 'identifier' => 'simple-icons:tailwindcss'],
            ['name' => 'PHP', 'identifier' => 'simple-icons:php'],
            ['name' => 'JavaScript', 'identifier' => 'simple-icons:javascript'],
            ['name' => 'MySQL', 'identifier' => 'simple-icons:mysql'],
            ['name' => 'Git', 'identifier' => 'simple-icons:git'],
            ['name' => 'Docker', 'identifier' => 'simple-icons:docker'],
            ['name' => 'Figma', 'identifier' => 'simple-icons:figma'],
            ['name' => 'Linux', 'identifier' => 'simple-icons:linux'],
        ];

        foreach ($skills as $skill) {
            Skill::create($skill);
        }
    }
}
