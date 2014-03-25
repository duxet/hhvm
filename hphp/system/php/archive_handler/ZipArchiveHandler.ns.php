<?php

namespace __SystemLib {
  final class ZipArchiveHandler extends ArchiveHandler {
    private \ZipArchive $za;
    private ?Map<string, ArchiveEntryData> $contents;
    private string $path = '';

    public function __construct(string $path) {
      $this->path = $path;
      $this->za = new \ZipArchive($path);
      $this->za->open($path);
    }

    public function getContentsList(): Map<string, ArchiveEntryStat> {
      $contents = $this->contents;
      if ($contents !== null) {
        return $contents;
      }

      $contents = Map { };
      for ($i = 0; $i < $this->za->numFiles; ++$i) {
        $fname = $this->za->getNameIndex($i);
        if (substr($fname, -1) === '/') {
          continue;
        }
        $stat = $this->za->statIndex($i);
        $contents[$fname] = new ArchiveEntryStat(
          $stat['crc'],
          $stat['size'],
          $stat['comp_size'],
          $stat['mtime'],
        );
      }
      $this->contents = $contents;
      return $contents;
    }

    public function read(string $path): ?string {
      $data = $this->za->getFromName($path);
      if ($data === false) {
        return null;
      }
      return $data;
    }

    public function extractAllTo(string $path): bool {
      return $this->za->extractTo($path);
    }
  }
}
