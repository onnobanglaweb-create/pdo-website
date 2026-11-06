# Bundled fonts

PDO Modern ships *no* binary font files in the repository so the theme stays
small and licence-clean. The stylesheet in this folder already declares the
`@font-face` rules; you only need to drop the matching files in:

| File | Family | Source |
| --- | --- | --- |
| `inter-variable.woff2` | Inter (variable, 100–900) | <https://github.com/rsms/inter> — SIL OFL 1.1 |
| `hind-siliguri-400.woff2` | Hind Siliguri Regular | <https://github.com/itfoundry/hind-siliguri> — SIL OFL 1.1 |
| `hind-siliguri-600.woff2` | Hind Siliguri Semibold | <https://github.com/itfoundry/hind-siliguri> — SIL OFL 1.1 |

When a file is absent the browser falls through to the next family in the
stack (`Helvetica Neue`/Arial for Latin, `Noto Sans Bengali`/`SolaimanLipi`
for Bengali), so the site still renders correctly.

Prefer not to host fonts at all? Switch **Customizer → Typography → Use the
bundled font files** off and pick a system stack instead. No Google Fonts
request is ever made — the theme never calls a CDN.
