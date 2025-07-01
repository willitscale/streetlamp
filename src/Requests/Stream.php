<?php

namespace willitscale\Streetlamp\Requests;

use Psr\Http\Message\StreamInterface;

// TODO: Better exception handling
class Stream implements StreamInterface
{
    private $stream;
    private ?int $size;

    public function __construct(
        string $stream = 'php://input',
        string $mode = 'r'
    ) {
        $this->stream = is_resource($stream) ? $stream : fopen($stream, $mode);
        if (!is_resource($this->stream)) {
            throw new \RuntimeException('Invalid stream resource');
        }
        $stats = fstat($this->stream);
        $this->size = $stats['size'] ?? null;
    }

    public function __toString(): string
    {
        try {
            $this->rewind();
            return stream_get_contents($this->stream);
        } catch (\Throwable $e) {
            return '';
        }
    }

    public function close(): void
    {
        if (is_resource($this->stream)) {
            fclose($this->stream);
        }
    }

    public function detach()
    {
        $result = $this->stream;
        $this->stream = null;
        $this->size = null;
        return $result;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function tell(): int
    {
        $result = ftell($this->stream);
        if ($result === false) {
            throw new \RuntimeException('Unable to determine stream position');
        }
        return $result;
    }

    public function eof(): bool
    {
        return feof($this->stream);
    }

    public function isSeekable(): bool
    {
        $meta = $this->getMetadata();
        return $meta['seekable'] ?? false;
    }

    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        if (!$this->isSeekable() || fseek($this->stream, $offset, $whence) !== 0) {
            throw new \RuntimeException('Unable to seek in stream');
        }
    }

    public function rewind(): void
    {
        $this->seek(0);
    }

    public function isWritable(): bool
    {
        $meta = $this->getMetadata();
        $mode = $meta['mode'] ?? '';
        return strpbrk($mode, 'wca+') !== false;
    }

    public function write(string $string): int
    {
        if (!$this->isWritable()) {
            throw new \RuntimeException('Stream is not writable');
        }
        $result = fwrite($this->stream, $string);
        if ($result === false) {
            throw new \RuntimeException('Unable to write to stream');
        }
        return $result;
    }

    public function isReadable(): bool
    {
        $meta = $this->getMetadata();
        $mode = $meta['mode'] ?? '';
        return strpbrk($mode, 'r+') !== false;
    }

    public function read(int $length): string
    {
        if (!$this->isReadable()) {
            throw new \RuntimeException('Stream is not readable');
        }
        $result = fread($this->stream, $length);
        if ($result === false) {
            throw new \RuntimeException('Unable to read from stream');
        }
        return $result;
    }

    public function getContents(): string
    {
        $this->rewind();
        $result = stream_get_contents($this->stream);
        if ($result === false) {
            throw new \RuntimeException('Unable to get contents of stream');
        }
        return $result;
    }

    public function getMetadata(?string $key = null)
    {
        $meta = is_resource($this->stream) ? stream_get_meta_data($this->stream) : [];
        if ($key === null) {
            return $meta;
        }
        return $meta[$key] ?? null;
    }
}
