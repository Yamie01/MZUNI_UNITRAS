protected function schedule(Schedule $schedule)
{
    // Run automatic vetting every hour
    $schedule->command('vehicles:vet-pending')->hourly();
}