<?php

namespace App\Console\Commands;

use App\Models\Master\Image;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FixBase64Images extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:fix-base64';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix incorrectly stored Base64 encoded images';

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
     * @return int
     */
    public function handle()
    {
       $images = Image::all();

        foreach ($images as $image) {
            $url = $image->url;
            $content = Storage::disk('images')->get($url);

            // Check if the content looks like a Base64 encoded image
            if (substr($content, 0, 11) == 'data:image/') {
                $image_base64 = substr($content, strpos($content, ",") + 1);
                $image_raw_data = base64_decode($image_base64);

                // Override the existing file with the decoded image data
                Storage::disk('images')->put($url, $image_raw_data);
                $this->info("Fixed image with URL: {$url}");
            }
        }

        $this->info('Image fixing process completed.');
    }
}
