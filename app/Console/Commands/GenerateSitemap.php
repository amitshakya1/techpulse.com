<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\SitemapGenerator;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate {domain?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the website sitemap';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating sitemap for www subdomain...');

        // Build the www subdomain URL
        $domain = 'www.' . ($domain = $this->argument('domain') ?? config('app.domain'));
        $protocol = config('app.env') === 'local' ? 'http' : 'https';
        $port = config('app.env') === 'local' ? ':8000' : '';
        $wwwUrl = "{$protocol}://{$domain}{$port}";

        $this->info("Crawling: {$wwwUrl}");

        SitemapGenerator::create($wwwUrl)
            ->shouldCrawl(function ($url) use ($wwwUrl) {
                // Only crawl URLs that belong to www subdomain
                $urlHost = $url->getScheme() . '://' . $url->getHost();
                $wwwHost = parse_url($wwwUrl, PHP_URL_SCHEME) . '://' . parse_url($wwwUrl, PHP_URL_HOST);
                return str_starts_with($urlHost, $wwwHost);
            })
            ->writeToFile(public_path("sitemaps/{$domain}.xml"));

        $this->info("✅ Sitemap generated successfully at public/sitemaps/{$domain}.xml");
    }
}
