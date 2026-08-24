# Large File Uploads

The Document Vault uses chunked uploads for large files. Each browser request contains a small chunk, so a low PHP `post_max_size` does not block the complete file at once.

For production, the web/PHP server must still permit the individual chunk size and have enough disk space. Chunking does not bypass hosting storage quotas.
