# Audit Ignore

Security advisories added to `composer.json` audit ignore configuration.

## Twig Template Engine

These CVEs affect `twig/twig` versions supported by this bundle (`^2.12 || ^3.3`):

| Advisory | CVE | Description |
|----------|-----|-------------|
| PKSA-yhcn-xrg3-68b1 | [CVE-2024-45411](https://www.cve.org/CVERecord?id=CVE-2024-45411) | Sandbox bypass via templates loaded in non-sandbox context |
| PKSA-2wrf-1xmk-1pky | [CVE-2024-51755](https://www.cve.org/CVERecord?id=CVE-2024-51755) | Unguarded `__isset()` allows attribute access on Array-like objects |
| PKSA-6319-ffpf-gx66 | [CVE-2022-39261](https://www.cve.org/CVERecord?id=CVE-2022-39261) | Filesystem loader path traversal |
| PKSA-n7sg-8f52-pqtf | [CVE-2022-23614](https://www.cve.org/CVERecord?id=CVE-2022-23614) | Sandbox bypass via `sort` filter |
| PKSA-8kk8-h2xr-h5nx | [CVE-2019-9942](https://www.cve.org/CVERecord?id=CVE-2019-9942) | Sandbox information disclosure |

These are ignored to maintain backward compatibility with Twig 2.x. Users should upgrade to Twig 3.x when possible.
