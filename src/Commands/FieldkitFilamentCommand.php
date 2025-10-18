<?php

namespace Ameax\FieldkitFilament\Commands;

use Illuminate\Console\Command;

class FieldkitFilamentCommand extends Command
{
    public $signature = 'fieldkit-filament';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
