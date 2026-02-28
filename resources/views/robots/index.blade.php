User-agent: *
@if($production)
Allow: /
@else
Disallow: /
@endif

Sitemap: {{ url('/sitemap.xml') }}
