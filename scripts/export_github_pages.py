#!/usr/bin/env python3
"""Export the existing Laravel public pages to a GitHub Pages project site.

The output contains rendered pages and their public assets.
"""
from __future__ import annotations

import argparse
import re
import shutil
import time
from html import escape as escape_attribute, unescape
from html.parser import HTMLParser
from pathlib import Path
from urllib.error import URLError
from urllib.parse import SplitResult, quote, unquote, urlsplit
from urllib.request import urlopen
from xml.sax.saxutils import escape

PROJECT = Path(__file__).resolve().parents[1]
PAGES = ["/", "/profil", "/maniis", "/subsektor", "/program", "/kegiatan", "/kontak"]
MUSIC_ROUTE = "/musik-latar.mp3"
IMAGE_EXTENSIONS = {".avif", ".gif", ".jpeg", ".jpg", ".png", ".webp"}
PUBLIC_EXTENSIONS = {
    "build": IMAGE_EXTENSIONS | {".css", ".js", ".mjs", ".svg", ".woff", ".woff2"},
    "images": IMAGE_EXTENSIONS,
}
ATTR_RE = re.compile(
    r'(?P<key>(?<![\w:-])(?:href|src|action|poster|content)\s*=\s*)'
    r'(?P<quote>["\x27])(?P<value>.*?)(?P=quote)',
    re.IGNORECASE | re.DOTALL,
)
CSS_ROOT_URL_RE = re.compile(
    r'url\(\s*(?P<quote>["\x27]?)(?P<path>/(?:build|images)/[^)\x27"\s]+)(?P=quote)\s*\)'
)
DETAIL_ROUTE_RE = re.compile(r"/(?:program|kegiatan)/[^/]+")


def local_url(value: str, origin: str, public_url: str) -> SplitResult | None:
    try:
        parsed = urlsplit(value)
    except ValueError:
        return None

    if not parsed.scheme and not parsed.netloc:
        return parsed if parsed.path.startswith("/") else None

    for base in (urlsplit(origin), urlsplit(public_url)):
        if (parsed.scheme, parsed.netloc) != (base.scheme, base.netloc):
            continue

        prefix = base.path.rstrip("/")
        if parsed.path == prefix or parsed.path.startswith(prefix + "/"):
            return parsed._replace(path=parsed.path[len(prefix):] or "/")

    return None


def detail_path(path: str) -> str | None:
    path = path.rstrip("/")
    if not DETAIL_ROUTE_RE.fullmatch(path):
        return None

    parent, slug = path.rsplit("/", 1)
    try:
        slug = unquote(slug, errors="strict")
    except UnicodeDecodeError:
        return None
    if slug in {".", ".."} or any(character in "/\\" or ord(character) < 32 for character in slug):
        return None

    return parent + "/" + quote(slug)


class Links(HTMLParser):
    def __init__(self, origin: str, public_url: str):
        super().__init__()
        self.origin = origin
        self.public_url = public_url
        self.paths: set[str] = set()

    def handle_starttag(self, tag, attrs):
        if tag != "a":
            return
        href = dict(attrs).get("href") or ""
        parsed = local_url(href, self.origin, self.public_url)
        if parsed and (path := detail_path(parsed.path)):
            self.paths.add(path)


def fetch_html(origin: str, path: str) -> str:
    with urlopen(origin + path, timeout=30) as response:
        content_type = response.headers.get("Content-Type", "")
        if "text/html" not in content_type:
            raise RuntimeError(f"Unexpected content type for {path}: {content_type}")
        return response.read().decode("utf-8")


def encode_page(html: str, origin: str, public_url: str, prefix: str, routes: set[str]) -> str:
    def rewrite(match: re.Match[str]) -> str:
        parsed = local_url(unescape(match.group("value")), origin, public_url)
        if parsed is None:
            return match.group(0)

        path = detail_path(parsed.path) or parsed.path.rstrip("/") or "/"
        if path in routes:
            path = path.rstrip("/") + "/"
        else:
            path = parsed.path

        root = public_url if parsed.scheme else prefix
        rewritten = root + path
        if parsed.query or parsed.fragment:
            if parsed.query:
                rewritten += "?" + parsed.query
            if parsed.fragment:
                rewritten += "#" + parsed.fragment
        return match.group("key") + match.group("quote") + escape_attribute(rewritten) + match.group("quote")

    return ATTR_RE.sub(rewrite, html)


def copy_public_assets(output: Path) -> None:
    for folder, extensions in PUBLIC_EXTENSIONS.items():
        source_dir = PROJECT / "public" / folder
        if source_dir.is_symlink():
            continue
        for source in source_dir.rglob("*"):
            relative = source.relative_to(source_dir)
            if (
                source.suffix.lower() in extensions
                and not any(part.startswith(".") for part in relative.parts)
                and not source.is_symlink()
                and source.is_file()
            ):
                target = output / folder / relative
                target.parent.mkdir(parents=True, exist_ok=True)
                shutil.copy2(source, target)


def export_music(origin: str, output: Path) -> None:
    with urlopen(origin + MUSIC_ROUTE, timeout=30) as response:
        if response.headers.get_content_type() != "audio/mpeg":
            raise RuntimeError(f"Unexpected content type for {MUSIC_ROUTE}")
        audio = response.read()
    if not audio:
        raise RuntimeError(f"No audio received for {MUSIC_ROUTE}")
    (output / MUSIC_ROUTE.lstrip("/")).write_bytes(audio)


def main() -> None:
    parser = argparse.ArgumentParser()
    parser.add_argument("--origin", default="http://127.0.0.1:8000")
    parser.add_argument("--site-url", default="https://fauziafrizal.github.io/ekraf-maniis")
    parser.add_argument("--output", default="_site")
    args = parser.parse_args()

    origin, site_url = args.origin.rstrip("/"), args.site_url.rstrip("/")
    prefix = urlsplit(site_url).path.rstrip("/")
    output = PROJECT / args.output
    if output.resolve() in (PROJECT, *PROJECT.parents):
        parser.error("--output must be a dedicated export directory")

    # Wait for the locally running Laravel server.
    for attempt in range(40):
        try:
            fetch_html(origin, "/")
            break
        except (OSError, URLError):
            if attempt == 39:
                raise RuntimeError("Laravel server was not ready on " + origin)
            time.sleep(1)

    if output.exists():
        shutil.rmtree(output)
    output.mkdir(parents=True)

    copy_public_assets(output)
    export_music(origin, output)
    robots = PROJECT / "public" / "robots.txt"
    if robots.is_file():
        shutil.copy2(robots, output / "robots.txt")
    (output / ".nojekyll").touch()

    pending = list(PAGES)
    completed: dict[str, str] = {}
    while pending:
        path = pending.pop(0)
        if path in completed:
            continue
        source_html = fetch_html(origin, path)
        links = Links(origin, site_url)
        links.feed(source_html)
        for linked in sorted(links.paths):
            if linked not in completed and linked not in pending:
                pending.append(linked)
        completed[path] = source_html

    routes = set(completed)
    for path, source_html in completed.items():
        encoded = encode_page(source_html, origin, site_url, prefix, routes)
        target_dir = output / unquote(path).lstrip("/")
        target_dir.mkdir(parents=True, exist_ok=True)
        (target_dir / "index.html").write_text(encoded, encoding="utf-8")
        print("Exported:", path, "->", target_dir / "index.html")

    # Vite fonts and public images may use root-relative CSS URLs.
    assets = output / "build" / "assets"
    if assets.exists():
        for file in assets.rglob("*.css"):
            css = file.read_text(encoding="utf-8")
            css = CSS_ROOT_URL_RE.sub(
                lambda match: "url(" + match.group("quote") + prefix
                + match.group("path") + match.group("quote") + ")",
                css,
            )
            file.write_text(css, encoding="utf-8")

    urls = [
        site_url + ("/" if path == "/" else path + "/")
        for path in completed
    ]
    sitemap = '<?xml version="1.0" encoding="UTF-8"?>\n'
    sitemap += '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n'
    sitemap += "".join(f"  <url><loc>{escape(url)}</loc></url>\n" for url in urls)
    sitemap += "</urlset>\n"
    (output / "sitemap.xml").write_text(sitemap, encoding="utf-8")
    print(f"Export complete: {len(completed)} public pages and assets in {output}")


if __name__ == "__main__":
    main()
