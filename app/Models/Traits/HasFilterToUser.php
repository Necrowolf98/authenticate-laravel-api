<?php

namespace App\Models\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

trait HasFilterToUser
{
    public function scopeFilter(Builder $query, $filters): Builder
    {
        if (!empty($filters->global->value)) {
            $globalValue = '%' . $filters->global->value . '%';
            $query->where(function ($query) use ($globalValue) {
                $query->where('name', 'like', $globalValue);
                $query->orWhere('lastname', 'like', $globalValue);
                $query->orWhere('direction', 'like', $globalValue);
                $query->orWhere('phone', 'like', $globalValue);
                $query->orWhere('email', 'like', $globalValue);
                $query->orWhere('created_at', 'like', $globalValue);
                $query->orWhereHas('roles', function ($query) use ($globalValue) {
                    $query->where('name', 'like', $globalValue);
                });
                $query->orWhereHas('permissions', function ($query) use ($globalValue) {
                    $query->where('name', 'like', $globalValue);
                });
            });
        }

        foreach ($filters as $field => $filterData) {
            if ($field === 'global') {
                continue;
            }

            $value = $filterData->constraints[0]->value ?? null;
            $matchMode = $filterData->constraints[0]->matchMode ?? 'equals';

            if (!empty($value)) {
                if ($matchMode === 'contains') {
                    
                    $value = '%' . $value . '%';
                    if ($field === 'roles.name') {
                        $query->whereHas('roles', function ($query) use ($value) {
                            $query->where('name', 'like', $value);
                        });
                    } else if($field === 'permissions.name') {
                        $query->whereHas('permissions', function ($query) use ($value) {
                            $query->where('name', 'like', $value);
                        });
                    }else {
                        $query->where($field, 'like', $value);
                    }
                } else if ($matchMode === 'equals') {
                    if ($field === 'roles.name') {
                        $query->whereHas('roles', function ($query) use ($value) {
                            $query->where('name', '=', $value);
                        });
                    } else if($field === 'permissions.name') {
                        $query->whereHas('permissions', function ($query) use ($value) {
                            $query->where('name', '=', $value);
                        });
                    }else {
                        $query->where($field, '=', $value);
                    }
                } else if ($matchMode === 'startsWith') {
                    if ($field === 'roles.name') {
                        $query->whereHas('roles', function ($query) use ($value) {
                            $query->where('name', 'like', $value.'%');
                        });
                    } else if($field === 'permissions.name') {
                        $query->whereHas('permissions', function ($query) use ($value) {
                            $query->where('name', 'like', $value.'%');
                        });
                    }else {
                        $query->where($field, 'like', $value . '%');
                    }
                }else if ($matchMode === 'notContains') {
                    $value = '%' . $value . '%';
                    if ($field === 'roles.name') {
                        $query->whereHas('roles', function ($query) use ($value) {
                            $query->where('name', 'not like', $value);
                        });
                    } else if($field === 'permissions.name') {
                        $query->whereHas('permissions', function ($query) use ($value) {
                            $query->where('name', 'not like', $value);
                        });
                    }else {
                        $query->where($field, 'not like', $value);
                    }
                }else if ($matchMode === 'endsWith'){
                    $value = '%' . $value;
                    if ($field === 'roles.name') {
                        $query->whereHas('roles', function ($query) use ($value) {
                            $query->where('name', 'like', '%'.$value);
                        });
                    } else if($field === 'permissions.name') {
                        $query->whereHas('permissions', function ($query) use ($value) {
                            $query->where('name', 'like', '%'.$value);
                        });
                    } else {
                        $query->where($field, 'like', $value);
                    }
                } else if ($matchMode === 'notEquals') {
                    if ($field === 'roles.name') {
                        $query->whereHas('roles', function ($query) use ($value) {
                            $query->where('name', '<>', $value);
                        });
                    } else if($field === 'permissions.name') {
                        $query->whereHas('permissions', function ($query) use ($value) {
                            $query->where('name', '<>', $value);
                        });
                    } else {
                        $query->where($field, '<>', $value);
                    }
                } else if ($matchMode === 'dateIs') {
                    if($field === 'created_at') {
                        $date = Carbon::parse(explode("T", $value)[0]);
                        $query->whereDate('created_at', '=', $date->format("Y-m-d"));
                    }
                } else if ($matchMode === 'dateIsNot') {
                    if($field === 'created_at') {
                        $date = Carbon::parse(explode("T", $value)[0]);
                        $query->whereDate('created_at', '<>', $date->format("Y-m-d"));
                    }
                } else if ($matchMode === 'dateBefore') {
                    if($field === 'created_at') {
                        $date = Carbon::parse(explode("T", $value)[0]);
                        $query->whereDate('created_at', '<', $date->format("Y-m-d"));
                    }
                } else if ($matchMode === 'dateAfter') {
                    if($field === 'created_at') {
                        $date = Carbon::parse(explode("T", $value)[0]);
                        $query->whereDate('created_at', '>', $date->format("Y-m-d"));
                    }
                }
            }
        }

        return $query;
    }

    public function scopeOrderByFilters(Builder $query, $decode_filter)
    {
        if (!is_null($decode_filter->sortField)) {

            if($decode_filter->sortField === 'lastname')
            {
                $query->orderBy('lastname', ($decode_filter->sortOrder === 1 ? 'asc' : 'desc'));
            }

            if($decode_filter->sortField === 'email')
            {
                $query->orderBy('email', ($decode_filter->sortOrder === 1 ? 'asc' : 'desc'));
            }

            if($decode_filter->sortField === 'phone')
            {
                $query->orderBy('phone', ($decode_filter->sortOrder === 1 ? 'asc' : 'desc'));
            }

            if($decode_filter->sortField === 'roles.name')
            {
                $query->whereHas('roles', function ($query) use ($decode_filter) {
                    $query->orderBy('name', ($decode_filter->sortOrder === 1 ? 'asc' : 'desc'));
                });
            }

            if($decode_filter->sortField === 'permissions.name')
            {
                $query->whereHas('permmissions', function ($query) use ($decode_filter) {
                    $query->orderBy('name', ($decode_filter->sortOrder === 1 ? 'asc' : 'desc'));
                });
            }
        }
    }
}