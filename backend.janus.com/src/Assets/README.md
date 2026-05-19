# Assets

Serves stored files as on-the-fly transformed image assets, applying resize, crop, and format conversion via PHP GD before streaming the binary response to the client.

---

## Folder Structure

```
Assets/
  Application/
    DTO/
      TransformedAssetDto.php   ← Carries the binary content, MIME type, and filename returned by the handler
      Tests/                    ← Unit tests for TransformedAssetDto
    Query/
      GetAssetQuery.php         ← Query payload: file UUID plus optional width, height, fit, and format
      Tests/                    ← Unit tests for GetAssetQuery
      Handler/
        GetAssetHandler.php     ← Resolves the file, validates physical existence, delegates to AssetTransformService
        Tests/                  ← Unit tests for GetAssetHandler
  Domain/
    Service/
      AssetTransformService.php ← Pure GD-based image transformer; no framework or database dependencies
      Tests/                    ← Unit tests for AssetTransformService
  Presentation/
    Controller/
      AssetsController.php      ← Thin HTTP controller; validates the request and streams the binary response
      Tests/                    ← Unit tests for AssetsController
```

---

## REST Endpoints

| Method | Path | Auth | Description |
|---|---|---|---|
| `GET` | `/assets/{id}` | Authenticated (WEB, CLI) | Returns the identified file as a transformed image binary |

---

## Query Parameters

| Parameter | Type | Default | Description |
|---|---|---|---|
| `width` | `int` | — | Target output width in pixels; clamped to a minimum of 1 |
| `height` | `int` | — | Target output height in pixels; clamped to a minimum of 1 |
| `fit` | `string` | `contain` | Resize strategy: `contain`, `cover`, or `fill`; falls back to `contain` if unrecognised |
| `format` | `string` | `jpg` | Output format: `jpg`, `png`, or `webp`; falls back to the file's own MIME type if unrecognised |

When both `width` and `height` are omitted the source image is served at its original dimensions.
When only one dimension is provided the other is derived proportionally before the fit is applied.

---

## Response Envelope

### Success — binary image stream

On success the response body is raw image binary (not JSON). The relevant headers are:

```
HTTP/1.1 200 OK
Content-Type: image/jpeg          (or image/png / image/webp)
Content-Disposition: inline; filename="original-name.jpg"
Cache-Control: public, max-age=31536000, immutable
```

### Error — file not found (404)

```json
{
  "errors": [
    { "message": "File \"<uuid>\" not found.", "extensions": { "code": "NOT_FOUND" } }
  ]
}
```

Returned when no file record exists for the given UUID, or when the physical file is missing from the storage directory.

### Error — transform failure (422)

```json
{
  "errors": [
    { "message": "Asset could not be processed.", "extensions": { "code": "TRANSFORM_ERROR" } }
  ]
}
```

Returned when PHP GD cannot decode or render the source file (e.g. a corrupt or unsupported image).

---

## Key Classes

| Class | File | Role |
|---|---|---|
| `AssetTransformService` | `Domain/Service/AssetTransformService.php` | Loads the source via GD, calculates target dimensions for the chosen fit mode, and renders the result to a binary string |
| `GetAssetHandler` | `Application/Query/Handler/GetAssetHandler.php` | Orchestrates the retrieval, physical-existence check, format/fit resolution, and transformation call |
| `TransformedAssetDto` | `Application/DTO/TransformedAssetDto.php` | Carries the raw binary content, resolved MIME type, and download filename across the application boundary |
| `GetAssetQuery` | `Application/Query/GetAssetQuery.php` | Immutable value object transporting the file UUID and transform parameters from the controller to the handler |

---

## External Dependencies

### Internal modules

| Module | Class / Interface used | Why |
|---|---|---|
| **Heimdall** | `RequestGuard` | Validates API version, authentication scope, and allowed client types (WEB, CLI) |
| **Files** | `FileRepositoryInterface` | Resolves file metadata (disk filename, MIME type, download name) by UUID |
| **Files** | `FileStorageService` | Resolves the absolute local filesystem path for a stored filename |

### Third-party packages

| Package | Used via | Why |
|---|---|---|
| PHP GD extension | `imagecreatefromjpeg()`, `imagepng()`, etc. | Image decoding, resampling, and encoding for all transform operations |
| Symfony HttpFoundation | `Request`, `Response` | Reads query parameters and constructs the binary or JSON HTTP response |
| Symfony FrameworkBundle | `AbstractController` | Provides `json()` for error responses |
