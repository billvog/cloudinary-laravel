<?php

namespace CloudinaryLabs\CloudinaryLaravel;

use Exception;
use CloudinaryLabs\CloudinaryLaravel\Model\Media;

/**
 * MediaAlly
 *
 * Provides functionality for attaching Cloudinary files to an eloquent model.
 * Whether the model should automatically reload its media relationship after modification.
 *
 */
trait MediaAlly {

    /**
     * Relationship for all attached media.
     */
    public function medially() {
        return $this->morphMany(Media::class, 'medially')->orderBy('order', 'asc');
    }


    /**
     * Attach Media Files to a Model
     */
    public function attachMedia($file, $order = 0, $options = []) {
        if (!file_exists($file)) {
            throw new Exception('Please pass in a file that exists');
        }

        $response = resolve(CloudinaryEngine::class)->uploadFile($file->getRealPath(), $options);

        $media = new Media();
        $media->file_name = $response->getFileName();
        $media->file_url = $response->getSecurePath();
        $media->size = $response->getSize();
        $media->file_type = $response->getFileType();
        $media->order = $order;

        $this->medially()->save($media);
    }

    /**
     * Attach Remote Media Files to a Model
     */
    public function attachRemoteMedia($remoteFile, $order = 0, $options = []) {
        $response = resolve(CloudinaryEngine::class)->uploadFile($remoteFile, $options);

        $media = new Media();
        $media->file_name = $response->getFileName();
        $media->file_url = $response->getSecurePath();
        $media->size = $response->getSize();
        $media->file_type = $response->getFileType();
        $media->order = $order;

        $this->medially()->save($media);
    }

    /**
     * Get all the Media files relating to a particular Model record
     */
    public function fetchAllMedia() {
        return $this->medially()->get();
    }

    /**
     * Get the first Media file relating to a particular Model record
     */
    public function fetchFirstMedia() {
        return $this->medially()->first();
    }

    /**
     * Delete all/one file(s) associated with a particular Model record
     */
    public function detachMedia(Media $media = null) {
        // If $media is null the expected behavior is to
        // delete all the media attached to our model
        // from cloudinary and our database.
        if (is_null($media)) {
            $items = $this->medially()->get();

            foreach ($items as $item) {
                resolve(CloudinaryEngine::class)->destroy($item->getFileName());
            }

            return $this->medially()->delete();
        }
        // Otherwise delete only the specified media from cloudinary 
        // and our database.
        else {
            resolve(CloudinaryEngine::class)->destroy($media->getFileName());
            return $media->delete();
        }
    }

    /**
     * Get the last Media file relating to a particular Model record
     */
    public function fetchLastMedia() {
        return $this->medially()->get()->last();
    }

    /**
     * Update the Media files relating to a particular Model record
     */
    public function updateMedia($file, $options = []) {
        $this->detachMedia();
        $this->attachMedia($file, $options);
    }

    /**
     * Update the Media files relating to a particular Model record (Specificially existing remote files)
     */
    public function updateRemoteMedia($file, $options = []) {
        $this->detachMedia();
        $this->attachRemoteMedia($file, $options);
    }
}
