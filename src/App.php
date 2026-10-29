<?php
declare(strict_types=1);

final class App
{
    public string $slug;
    public string $name;
    public string $tagline;
    public string $description;
    public string $url;
    public Category $category;
    public string $screenshot;
    public ?string $badge;

    public function __construct(
        string $slug,
        string $name,
        string $tagline,
        string $description,
        string $url,
        Category $category,
        string $screenshot,
        ?string $badge = null,
    ) {
        $this->slug = $slug;
        $this->name = $name;
        $this->tagline = $tagline;
        $this->description = $description;
        $this->url = self::validateUrl($url);
        $this->category = $category;
        $this->screenshot = $screenshot;
        $this->badge = $badge;
    }

    private static function validateUrl(string $url): string
    {
        $parts = parse_url($url);

        if (
            filter_var($url, FILTER_VALIDATE_URL) === false
            || $parts === false
            || strtolower((string) ($parts['scheme'] ?? '')) !== 'https'
            || empty($parts['host'])
            || isset($parts['user'])
            || isset($parts['pass'])
        ) {
            throw new InvalidArgumentException('App URL must be a valid HTTPS URL without embedded credentials.');
        }

        return $url;
    }
}
