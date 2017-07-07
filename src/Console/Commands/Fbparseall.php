<?php

namespace Ry\Socin\Console\Commands;

use Illuminate\Console\Command;
use Facebook\Facebook;
use Facebook\GraphNodes\GraphNode;
use Ry\Socin\Models\Facebooknode;
use Ry\Socin\Models\FacebookSource;
use Illuminate\Database\Eloquent\Model;

class Fbparseall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'socin:fbparse';

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
    	
    	$sources = FacebookSource::all();
    	
    	Model::unguard();
    	
    	foreach($sources as $source) {
    		$products = $fb->get($source->endpoint, $source->access_token)->getGraphEdge();
    		$ar = $products->all();
    		
    		$broken = false;
    		
    		foreach($ar as $a) {
    			if(Facebooknode::where("fbid", "=", $a->getField("id"))->where("source_id", "=", $source->id)->exists()) {
    				$broken = true;
    				break;
    			}
    			
    			/* @var $a GraphNode */
    			Facebooknode::updateOrCreate(["fbid" => $a->getField("id"), "source_id" => $source->id], [
    					"fbid" => $a->getField("id"),
    					"fbcreated" => $a->getField("created_time"),
    					"source_id" => $source->id
    			]);
    		}
    		
    		if(!$broken) {
    			while($products = $fb->next($products)) {
    				$ar = $products->all();
    				foreach($ar as $a) {
    					/* @var $a GraphNode */
    					Facebooknode::updateOrCreate(["fbid" => $a->getField("id"), "source_id" => $source->id], [
    							"fbid" => $a->getField("id"),
    							"fbcreated" => $a->getField("created_time"),
    							"source_id" => $source->id
    					]);
    				}
    				$this->info("mis hafa koa");
    			}
    		}
    	}
    	
    	Model::reguard();
		//$products = $fb->get("/1700007833570971/feed?fields=id,from,message,full_picture,picture,comments{comment_count,message,message_tags},attachments{subattachments},created_time&limit=5", env("ry_media_token"))->getGraphEdge();
    }
    /*
    public function getArguments() {
    	return ["name", "endpoint"];
    }
    */
}
