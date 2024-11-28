<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class JsonController extends Controller
{
    /**
     * Lista los archivos JSON disponibles en el almacenamiento.
     *
     * @return JsonResponse
     */
    public function index()
    {
        // Obtener los archivos de tipo .json de la carpeta 'app'
        $files = Storage::files('app');
    
        // Filtrar solo los archivos con extensión .json
        $validJsonFiles = array_filter($files, function($file) {
            return pathinfo($file, PATHINFO_EXTENSION) === 'json';
        });
    
        // Extraer solo el nombre del archivo (sin la ruta) usando basename()
        $validJsonFiles = array_map(function($file) {
            return basename($file); // Extraer solo el nombre del archivo
        }, $validJsonFiles);
    
        // Reindexar el array para asegurarnos de que sea numérico
        $validJsonFiles = array_values($validJsonFiles);
    
        return response()->json([
            'mensaje' => 'Operación exitosa',
            'contenido' => $validJsonFiles
        ]);
    }
    /**
     * Guarda un nuevo archivo JSON en el almacenamiento.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        // Validar que el nombre del archivo termine en .json y el contenido sea válido
        $request->validate([
            'filename' => 'required|string|regex:/^.+\.json$/',
            'content' => 'required|string',
        ]);

        // Verificar si el archivo ya existe
        if (Storage::exists('app/' . $request->filename)) {
            return response()->json(['mensaje' => 'El fichero ya existe'], 409);
        }

        // Intentar guardar el archivo
        try {
            // Validar si el contenido es un JSON válido
            $decodedContent = json_decode($request->content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['mensaje' => 'Contenido no es un JSON válido'], 415);
            }

            // Guardar el archivo en el almacenamiento
            Storage::put('app/' . $request->filename, json_encode($decodedContent, JSON_PRETTY_PRINT));
            return response()->json(['mensaje' => 'Fichero guardado exitosamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['mensaje' => 'Error al guardar el fichero'], 500);
        }
    }

    /**
     * Muestra el contenido de un archivo JSON.
     *
     * @param  string  $filename
     * @return JsonResponse
     */
    public function show($filename)
    {
        if (!Storage::exists('app/' . $filename)) {
            return response()->json(['mensaje' => 'El fichero no existe'], 404);
        }

        $content = Storage::get('app/' . $filename);
        return response()->json([
            'mensaje' => 'Operación exitosa',
            'contenido' => json_decode($content, true),
        ]);
    }

    /**
     * Actualiza el contenido de un archivo JSON existente.
     *
     * @param  Request  $request
     * @param  string  $filename
     * @return JsonResponse
     */
    public function update(Request $request, $filename)
    {
        // Verificar si el archivo existe
        if (!Storage::exists('app/' . $filename)) {
            return response()->json(['mensaje' => 'El fichero no existe'], 404);
        }

        // Validar que el contenido sea un JSON válido
        $request->validate([
            'content' => 'required|string',
        ]);

        // Intentar decodificar el contenido
        $decodedContent = json_decode($request->content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['mensaje' => 'Contenido no es un JSON válido'], 415);
        }

        // Guardar el archivo actualizado con formato bonito (con saltos de línea)
        Storage::put('app/' . $filename, json_encode($decodedContent));

        // Verificar que el contenido se ha actualizado correctamente
        $updatedContent = json_decode(Storage::get('app/' . $filename), true); // Decodificar el contenido del archivo

        // Comparar solo los datos, no el formato
        if ($updatedContent !== $decodedContent) {
            return response()->json(['mensaje' => 'Error al actualizar el fichero'], 500);
        }

        return response()->json(['mensaje' => 'Fichero actualizado exitosamente'], 200);
    }


    /**
     * Elimina un archivo JSON existente.
     *
     * @param  string  $filename
     * @return JsonResponse
     */
    public function destroy($filename)
    {
        if (!Storage::exists('app/' . $filename)) {
            return response()->json(['mensaje' => 'El fichero no existe'], 404);
        }

        Storage::delete('app/' . $filename);
        return response()->json(['mensaje' => 'Fichero eliminado exitosamente'], 200);
    }
}



