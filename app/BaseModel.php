<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\DeletedRecord;

class BaseModel extends Model
{

    public static $notRemoved = '0';

    public static $removed = '1';

    protected $hidden = ['is_removed', 'created_at', 'updated_at'];

    public static $havings = [];

    public static $removedColumn = 'is_removed';

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    public static function disableAuditing()
    {
        self::$auditingDisabled = true;
    }

    public static function enableAuditing()
    {
        self::$auditingDisabled = false;
    }

    public function newQuery() {
        try {
            $tableFillables = $this->fillable;
            $tableColumns   = \Schema::getColumnListing(parent::getTable());

            /*if (!empty(self::$havings)) {
                $this::disableAuditing();
            } else {
                $this::enableAuditing();
            }*/

            if (in_array(self::$removedColumn, $tableFillables) && in_array(self::$removedColumn, $tableColumns)) {
                return parent::newQuery()->where(parent::getTable() . '.is_removed', '=', self::$notRemoved);
            }
        } catch(Exception $exception) {}

        return parent::newQuery();
    }

    /*public static function updateOrCreate($data)
    {
        if (!\Auth::check()) {
            throw new \Exception("You can not create a post as a guest.");
        }

        if (isset(self::$auditingDisabled)) {
            self::$auditingDisabled = false;
            if (!empty(self::$havings)) {
                if (in_array(self::$auditedId, $data) && in_array($data[self::$auditedId], self::$havings)) {
                    self::$auditingDisabled = true;
                }
            }
        }

        return parent::updateOrCreate($data);
    }*/

    public static function isRemoveFire($data = [])
    {
        if (!empty($data) && !$data->isEmpty()) {
            foreach ($data as $record) {
                $tableName = get_class($record);
                $update    = $record->find($record->id);
                $data      = json_encode($update->attributesToArray());
                $deletedBy = \Auth::user()->id;
                $ip        = request()->ip();

                $create = DeletedRecord::create([
                    'model'      => $tableName,
                    'data'       => $data,
                    'deleted_by' => $deletedBy,
                    'ip'         => $ip
                ]);

                if ($create) {
                    $update->delete();
                }
            }
        }
    }
}
