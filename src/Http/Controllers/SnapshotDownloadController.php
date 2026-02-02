<?php

namespace MrFelipeMartins\Helix\Http\Controllers;

use Illuminate\Http\Request;
use MrFelipeMartins\Helix\Models\Snapshot;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SnapshotDownloadController
{
    public function __invoke(Request $request, Snapshot $snapshot): BinaryFileResponse
    {
        abort_unless(file_exists($snapshot->path), 404);

        return response()->download($snapshot->path, basename($snapshot->path));
    }
}
