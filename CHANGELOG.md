## 1.0.0
- Initial release on GitHub

## 1.1.0
- Removed checkout logic for getting the client ID, it's now fetched from the _ga cookie
- Removed Setup scripts and instead use a db_schema.xml file to create needed tables
- Fixed sort order admin settings

## 1.1.1
- Fix default value being 0 instead of null (expected value)

## 2.0.0-RC1
- Refactored to support Google Analytics 4, see UPGRADE_GUIDE.md to upgrade from 1.x to 2.x

## 2.0.0-RC2
- AnalyticsService method handleCancelledOrder is now defined as public.