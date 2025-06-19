<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        try {
            $students = Student::with(['createdBy'])->get();

            return response()->json($students, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching students.'], 500);
        }
    }

    public function show($id)
    {
        try {
            $student = Student::findOrFail($id);

            return response()->json($student, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Student not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching the student.'], 500);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'nis' => 'required|string|max:8|unique:students,nis',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email',
            'phone' => 'nullable|string|max:15|unique:students,phone',
            'address' => 'nullable|string|max:255',
            'date_of_birth' => 'required|date',
            'place_of_birth' => 'required|string|max:255',
            'class_name' => 'required|string|max:50',
        ]);

        try {
            $student = Student::create([
                'nis' => $request->input('nis'),
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'address' => $request->input('address'),
                'date_of_birth' => $request->input('date_of_birth'),
                'place_of_birth' => $request->input('place_of_birth'),
                'class_name' => $request->input('class_name'),
            ]);

            $student->load('createdBy');

            return response()->json($student, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while creating the student.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'nis' => 'required|string|max:8|unique:students,nis,' . $id,
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email,' . $id,
            'phone' => 'nullable|string|max:15|unique:students,phone,' . $id,
            'address' => 'nullable|string|max:255',
            'date_of_birth' => 'required|date',
            'place_of_birth' => 'required|string|max:255',
            'class_name' => 'required|string|max:50',
        ]);

        try {
            $student = Student::findOrFail($id);
            $student->update([
                'nis' => $request->input('nis'),
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'address' => $request->input('address'),
                'date_of_birth' => $request->input('date_of_birth'),
                'place_of_birth' => $request->input('place_of_birth'),
                'class_name' => $request->input('class_name'),
            ]);

            $student->load('createdBy');

            return response()->json($student, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Student not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while updating the student.', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $student = Student::findOrFail($id);
            $student->delete();

            return response()->json(['message' => 'Student deleted successfully.'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Student not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while deleting the student.'], 500);
        }
    }
}
