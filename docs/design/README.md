# BloomFeed interface update

The interface uses flat surfaces, a sunflower accent, and shared styles for the public pages and the reader. Both themes use the same spacing and type scale.

## Reading

Articles support title and source search, unread and favorite filters, and a compact view. Feed links and pagination preserve the active filters. Favorites work through ordinary form submissions, with asynchronous updates and error feedback when JavaScript is available.

On small screens, navigation and subscriptions collapse. Long subscription lists scroll within the sidebar, keeping its actions available.

## Language

English is the default. French, Spanish, and German remain available. Saved account and session preferences take precedence over the instance default.

## Checks

The update includes feature tests for search, account isolation, favorites, pagination, and page rendering. Browser checks covered widths from 320 to 1440 pixels, keyboard menu dismissal, compact mode, filters, and failed requests.

The product screenshots use a local demonstration account and sample articles. No production data is included.
