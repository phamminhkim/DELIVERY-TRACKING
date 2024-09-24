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

class SaveRawPoData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 0;
    public $maxExceptions = 0;

    protected $po_data = [];
    protected $po_file_path;
    protected $po_file_name;
    protected $convert_po_uid;

    protected $ai_repository;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($po_data, $po_file_path, $po_file_name, $convert_po_uid)
    {
        $this->po_data = $po_data;
        $this->po_file_path = $po_file_path;
        $this->po_file_name = $po_file_name;
        $this->convert_po_uid = $convert_po_uid;
        $this->ai_repository = new AiRepository(new LocalFileService(),
            new CamelotExtractorService(), new LeagueCsvConverter(), new IndexArrayMappingRestructure(),
            new CamelotExtractorService(), new LeagueCsvConverter(), new MergeIndexArrayMappingRestructure(),
            null,
            new Request());
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->ai_repository->saveRawPoData($this->po_data, $this->po_file_path, $this->po_file_name, $this->convert_po_uid);
    }
}
