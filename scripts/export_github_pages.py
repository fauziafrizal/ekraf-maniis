#!/usr/bin/env python3
"""Export the existing Laravel public pages to a GitHub Pages project site.

PHP runs only inside GitHub Actions; _site contains HTML/CSS/JS/images.
No credentials, database, .env, or PHP sources are published.
"""
from __future__ import annotations

import argparse
import re
import shutil
import time
from html.parser import HTMLParser
from pathlib import Path
from urllib.error import URLError
from urllib.parse import urlsplit
from urllib.request import urlopen
from xml.sax.saxutils import escape

PROJECT = Path(__file__).resolve().parents[1]
PAGES = ["/", "/profil", "/maniis", "/subsektor", "/program", "/kegiatan", "/kontak"]
ATTR_RE = re.compile(r'(?P<key>\b(?:href|src|action|poster)\s*=\s*)(?P<quote>["\x27])/(?P<path>[^"\x27]*)(?P=quote)')
CSS_ROOT_URL_RE = re.compile(r'url\(\s*(?P<quote>["\x27]?)/(?P<path>images/[^)\x27"\s]+)(?P=quote)\s*\)')


class Links(HTMLParser):
    def __init__(self):
        super().__init__()
        self.paths: set[str] = set()

    def handle_starttag(self, tag, attrs):
        if tag != "a":
            return
        href = dict(attrs).get("href", "")
        if not href.startswith("/"):
            return
        path = urlsplit(href).path.rstrip("/")
        if path.startswith(("/program/", "/kegiatan/")):
            self.paths.add(path)


def fetch_html(origin: str, path: str) -> str:
    with urlopen(origin + path, timeout=30) as response:
        content_type = response.headers.get("Content-Type", "")
        if "text/html" not in content_type:
            raise RuntimeError(f"Unexpected content type for {path}: {content_type}")
        return response.read().decode("utf-8")


def encode_page(html: str, origin: str, public_url: str, prefix: str, routes: set[str]) -> str:
    # Laravel's asset() and @vite may emit absolute local URLs.
    html = html.replace(origin + "/", public_url + "/")

    def rewrite(match: re.Match[str]) -> str:
        path = "/" + match.group("path")
        parsed = urlsplit(path)
        normalized = parsed.path.rstrip("/") or "/"
        # GitHub Pages serves these as directories with index.html.
        if normalized in routes:
            new_path = prefix + ("" if normalized == "/" else normalized) + "/"
        else:
            new_path = prefix + path
        if parsed.query or parsed.fragment:
            new_path = prefix + ("" if normalized == "/" else normalized) + (
                "/" if normalized in routes else ""
            )
            if parsed.query:
                new_path += "?" + parsed.query
            if parsed.fragment:
                new_path += "#" + parsed.fragment
        return match.group("key") + match.group("quote") + new_path + match.group("quote")

    return ATTR_RE.sub(rewrite, html)


def main() -> None:
    parser = argparse.ArgumentParser()
    parser.add_argument("--origin", default="http://127.0.0.1:8000")
    parser.add_argument("--site-url", default="https://fauziafrizal.github.io/ekraf-maniis")
    parser.add_argument("--output", default="_site")
    args = parser.parse_args()

    origin, site_url = args.origin.rstrip("/"), args.site_url.rstrip("/")
    prefix = urlsplit(site_url).path.rstrip("/")
    output = PROJECT / args.output

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

    # Copy only intended public assets; no PHP, .env, or SQLite files.
    for name in ("build", "images"):
        source = PROJECT / "public" / name
        if source.is_dir():
            shutil.copytree(source, output / name)
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
        links = Links()
        links.feed(source_html)
        for linked in sorted(links.paths):
            if linked not in completed and linked not in pending:
                pending.append(linked)
        completed[path] = source_html

    routes = set(completed)
    for path, source_html in completed.items():
        encoded = encode_page(source_html, origin, site_url, prefix, routes)
        target_dir = output / path.lstrip("/")
        target_dir.mkdir(parents=True, exist_ok=True)
        (target_dir / "index.html").write_text(encoded, encoding="utf-8")
        print("Exported:", path, "->", target_dir / "index.html")

    # Tailwind/Vite CSS can contain absolute URLs for public/images.
    # Under a project Pages URL those URLs need the repo prefix.
    assets = output / "build" / "assets"
    if assets.exists():
        for file in assets.rglob("*.css"):
            css = file.read_text(encoding="utf-8")
            css = CSS_ROOT_URL_RE.sub(
                lambda match: "url(" + match.group("quote") + prefix + "/"
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
