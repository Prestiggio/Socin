<?php

namespace Ry\Socin\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class Colonize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rysocin:newapp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crée un systeme basique pour acceder a un app iframe';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
    	$file = new Filesystem();
    	$ar = $file->directories(base_path("vendor/ry"));
    	$ar = array_prepend($ar, base_path("app"));
    	$path = $this->choice("Quel module ?", $ar);
    }
}
