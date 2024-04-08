<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Repositories\Business\AiRepository;
use App\Services\Implementations\Converters\LeagueCsvConverter;
use App\Services\Implementations\Extractors\CamelotExtractorService;
use App\Services\Implementations\Files\LocalFileService;
use App\Services\Implementations\Restructurers\IndexArrayMappingRestructure;
use App\Services\Implementations\Restructurers\MergeIndexArrayMappingRestructure;
use Illuminate\Http\Request;

class ExtractFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 0;
    public $maxExceptions = 0;


    protected $file_id;

    protected $ai_repostitory;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($file_id)
    {
        $this->file_id = $file_id;
        $this->ai_repostitory = new AiRepository(new LocalFileService(),
            new CamelotExtractorService(), new LeagueCsvConverter(), new IndexArrayMappingRestructure(),
            new CamelotExtractorService(), new LeagueCsvConverter(), new MergeIndexArrayMappingRestructure(),
            new Request());
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->ai_repostitory->extractOrderFromUploadedFile($this->file_id);
    }
}
