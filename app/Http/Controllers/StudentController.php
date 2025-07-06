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

    /**
     * @OA\Get(
     *     path="/api/students",
     *     security={{"bearerAuth":{}}},
     *     summary="Get list of students",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    public function index()
    {
        try {
            $students = Student::with(['createdBy'])->get();

            return response()->json($students, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching students.'], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/students/{id}",
     *     security={{"bearerAuth":{}}},
     *     summary="Get student by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Student not found"
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/students",
     *     security={{"bearerAuth":{}}},
     *     summary="Create a new student",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"nis", "name", "email", "date_of_birth", "place_of_birth", "class_name"},
     *             @OA\Property(property="nis", type="string", maxLength=8, example="12345678"),
     *             @OA\Property(property="name", type="string", maxLength=255, example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="phone", type="string", maxLength=15, example="08123456789"),
     *             @OA\Property(property="address", type="string", maxLength=255, example="Jl. Mawar No. 1"),
     *             @OA\Property(property="date_of_birth", type="string", format="date", example="2000-01-01"),
     *             @OA\Property(property="place_of_birth", type="string", maxLength=255, example="Jakarta"),
     *             @OA\Property(property="class_name", type="string", maxLength=50, example="XII IPA 1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Student created successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
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

    /**
     * @OA\Put(
     *     path="/api/students/{id}",
     *     security={{"bearerAuth":{}}},
     *     summary="Update a student",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"nis", "name", "email", "date_of_birth", "place_of_birth", "class_name"},
     *             @OA\Property(property="nis", type="string", maxLength=8, example="12345678"),
     *             @OA\Property(property="name", type="string", maxLength=255, example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="phone", type="string", maxLength=15, example="08123456789"),
     *             @OA\Property(property="address", type="string", maxLength=255, example="Jl. Mawar No. 1"),
     *             @OA\Property(property="date_of_birth", type="string", format="date", example="2000-01-01"),
     *             @OA\Property(property="place_of_birth", type="string", maxLength=255, example="Jakarta"),
     *             @OA\Property(property="class_name", type="string", maxLength=50, example="XII IPA 1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Student updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Student not found"
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/students/{id}",
     *     security={{"bearerAuth":{}}},
     *     summary="Delete a student",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Student deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Student not found"
     *     )
     * )
     */
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
