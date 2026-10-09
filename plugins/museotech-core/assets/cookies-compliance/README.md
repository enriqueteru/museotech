Vendored from `@solvenza/cookies-compliance` npm package, version 1.5.3.

- Source: https://www.npmjs.com/package/@solvenza/cookies-compliance
- Upstream file: `dist/consent.min.js`
- License: MIT; see `LICENSE` alongside the bundle.
- Registry integrity: `sha512-WG6dOtpJReyjz/g+4TcfM2AIJ8han/hp4GbXzW6iIJO0y+2whZqiXyvZzAAXNcuMI7Q3ltm8kKx8hQqKIr9Bdw==`
- Local fix for 1.5.3: removed the banner renderer's duplicate `solvenza:show` dispatch, which recursively invoked its own show-event listener and caused a `RangeError` on first visit.

The SDK only gates scripts, iframes, and images explicitly marked with its consent attributes. Review any new third-party resource and assign its category/service before deployment; this integration does not certify legal compliance by itself.
