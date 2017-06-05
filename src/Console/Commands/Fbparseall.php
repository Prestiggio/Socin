<?php

namespace Ry\Socin\Console\Commands;

use Illuminate\Console\Command;
use Facebook\Facebook;
use Facebook\GraphNodes\GraphNode;
use Ry\Socin\Models\Facebooknode;

class Fbparseall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rysocin:fbparse {name} {endpoint}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
    	$fb = new Facebook([
				'app_id' => "691462271025098",
				"app_secret" => "635f60e1510231ea5bb5cae9a3f60b47",
				"default_graph_version" => "v2.9"
		]);
		 
		//$products = $fb->get("/1700007833570971/feed?fields=id,from,message,full_picture,picture,comments{comment_count,message,message_tags},attachments{subattachments},created_time&limit=5", env("ry_media_token"))->getGraphEdge();
		$endpoint = $this->argument("endpoint");
		$name = $this->argument("name");
		$products = $fb->get($endpoint, env("ry_media_token"))->getGraphEdge();
		$ar = $products->all();
		$inserts = [];
		foreach($ar as $a) {
			/* @var $a GraphNode */
			$inserts[] = [
					"fbid" => $a->getField("id"),
					"fbcreated" => $a->getField("created_time"),
					"endpoint" => $endpoint,
					"name" => $name
			];
		}
		Facebooknode::insert($inserts);
    }
    
    public function getArguments() {
    	return ["name", "endpoint"];
    }
}
