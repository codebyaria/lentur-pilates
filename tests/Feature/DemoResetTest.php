<?php

namespace Tests\Feature;

use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Tests\TestCase;

class DemoResetTest extends TestCase
{
    private function resetEvent(): Event
    {
        $event = collect(app(Schedule::class)->events())
            ->first(fn (Event $event) => str_contains((string) $event->command, 'migrate:fresh --seed --force'));

        $this->assertNotNull($event, 'Reset demo malam tidak terjadwal.');

        return $event;
    }

    public function test_demo_resets_every_night_at_three_jakarta_time()
    {
        $event = $this->resetEvent();

        $this->assertSame('0 3 * * *', $event->expression);
        $this->assertSame('Asia/Jakarta', $event->timezone);
    }

    public function test_reset_only_runs_on_the_production_demo()
    {
        $event = $this->resetEvent();

        config(['app.demo' => false]);
        $this->assertFalse($event->filtersPass($this->app));

        config(['app.demo' => true]);
        $this->assertTrue($event->filtersPass($this->app));
        $this->assertFalse($event->runsInEnvironment('local'));
        $this->assertTrue($event->runsInEnvironment('production'));
    }
}
