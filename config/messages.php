<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Generic CRUD Messages
    |--------------------------------------------------------------------------
    |
    | Use ":attribute" as a placeholder for the model or entity name.
    |
    */
    'curd' => [
        'created' => ':attribute created successfully.',
        'updated' => ':attribute updated successfully.',
        'deleted' => ':attribute deleted successfully.',
        'exported' => ':attribute exported successfully.',
        'imported' => ':attribute imported successfully.',
        'bulk_action' => 'Bulk action performed successfully on :attribute.',
        'restored' => ':attribute restored successfully.',
        'trashed' => ':attribute trashed successfully.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Generic Error messages
    |--------------------------------------------------------------------------
    */
    'errors' => [
        'not_found' => ':attribute not found.',
        'failed_create' => 'Failed to create :attribute. Please try again later.',
        'failed_update' => 'Failed to update :attribute. Please try again later.',
        'failed_delete' => 'Failed to delete :attribute. Please try again later.',
        'failed_export' => 'Failed to export :attribute.',
        'failed_import' => 'Failed to import :attribute.',
        'failed_bulk_action' => 'Failed to perform bulk action on :attribute.',
        'failed_restore' => 'Failed to restore :attribute.',
        'failed_trashed' => 'Failed to trash :attribute.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Other general messages
    |--------------------------------------------------------------------------
    */
    'unauthorized' => 'You are not authorized to perform this action.',
    'server_error' => 'Something went wrong. Please try again later.',
];
