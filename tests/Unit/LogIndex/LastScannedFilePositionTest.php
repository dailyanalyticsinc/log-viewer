<?php

it('can set the last-scanned file position', function () {
    $logIndex = createLogIndex();

    $logIndex->setLastScannedFilePosition($position = 1500);

    expect($logIndex->getLastScannedFilePosition())->toBe($position);
});

it('remembers the last-scanned file position', function () {
    $logIndex = createLogIndex();
    $metaCacheKey = $logIndex->metaCacheKey();

    $logIndex->setLastScannedFilePosition($position = 1500);

    // At first, it won't be cached because it's data waiting to be saved.
    expect(Cache::get($metaCacheKey)['last_scanned_file_position'])
        ->toBe(0);

    $logIndex->save();

    expect($metaCacheResult = Cache::get($metaCacheKey))
        ->toHaveKey('last_scanned_file_position')
        ->and($metaCacheResult['last_scanned_file_position'])->toBe($position);
});

it('fetches the last-scanned file position from the cache first', function () {
    $logIndex = createLogIndex();
    Cache::put(
        $logIndex->metaCacheKey(),
        ['last_scanned_file_position' => $position = 5000],
    );
    // re-instantiate so it picks up the new cache value
    $logIndex = createLogIndex($logIndex->getFile());

    expect($logIndex->getLastScannedFilePosition())->toBe($position);

    // Make sure to clean up!
    Cache::forget($logIndex->metaCacheKey());
});
