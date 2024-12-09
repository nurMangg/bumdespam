<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseController extends Controller
{
    protected $model;

    public function edit($id)
    {
        $model = app($this->model);
        $data = $model->find($id);

        if (!$data) {
            return response()->json(['error' => 'Data not found'], 404);
        }

        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $model = app($this->model);
        $data = $model->find($id);

        if (!$data) {
            return response()->json(['error' => 'Data not found'], 404);
        }

        $data->update($request->all());

        return response()->json(['message' => 'Data updated successfully', 'data' => $data]);
    }

    public function destroy($id)
    {
        $model = app($this->model);
        $data = $model->find($id);

        if (!$data) {
            return response()->json(['error' => 'Data not found'], 404);
        }

        $data->delete();

        return response()->json(['message' => 'Data deleted successfully']);
    }
}
