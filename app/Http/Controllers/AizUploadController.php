<?php

namespace App\Http\Controllers;

use App\Models\Invoice_upload;
use Illuminate\Http\Request;
use App\Models\Upload;
use Response;
use Auth;
use Route;
use Illuminate\Support\Facades\Storage;
use Image;
use enshrined\svgSanitize\Sanitizer;
use Illuminate\Support\Str;



class AizUploadController extends Controller
{
    public function index(Request $request)
    {
        $all_uploads = (auth()->user()->user_type == 'seller')
            ? Upload::where('user_id', auth()->user()->id)->where('invoice_file', 0)
            : Upload::query()->where('invoice_file', 0);

        $search = null;
        $sort_by = null;

        if ($request->search != null) {
            $search = $request->search;
            $all_uploads->where('file_original_name', 'like', '%' . $request->search . '%');
        }

        $sort_by = $request->sort;
        switch ($request->sort) {
            case 'newest':
                $all_uploads->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $all_uploads->orderBy('created_at', 'asc');
                break;
            case 'smallest':
                $all_uploads->orderBy('file_size', 'asc');
                break;
            case 'largest':
                $all_uploads->orderBy('file_size', 'desc');
                break;
            default:
                $all_uploads->orderBy('created_at', 'desc');
                break;
        }

        $all_uploads = $all_uploads->paginate(60)->appends(request()->query());


        return (auth()->user()->user_type == 'seller')
            ? view('seller.uploads.index', compact('all_uploads', 'search', 'sort_by'))
            : view('backend.uploaded_files.index', compact('all_uploads', 'search', 'sort_by'));
    }

    public function create()
    {
        return (auth()->user()->user_type == 'seller')
            ? view('seller.uploads.create')
            : view('backend.uploaded_files.create');
    }

    public function uploaded_create()
    {
        return view('backend.uploaded_invoice.create');
    }

    public function invoice_create()
    {
        return (auth()->user()->user_type == 'seller')
            ? view('seller.uploads.create')
            : view('backend.uploaded_files.invoice-create');
    }

    public function uploaded_sales_create()
    {
        return view('backend.uploaded_invoice.sales.create');
    }

    public function show_uploader(Request $request)
    {
        return view('uploader.aiz-uploader');
    }

    // public function upload(Request $request, $is_invoice = 0)
    // {

    //     $type = array(
    //         "jpg" => "image",
    //         "jpeg" => "image",
    //         "png" => "image",
    //         "svg" => "image",
    //         "webp" => "image",
    //         "gif" => "image",
    //         "mp4" => "video",
    //         "mpg" => "video",
    //         "mpeg" => "video",
    //         "webm" => "video",
    //         "ogg" => "video",
    //         "avi" => "video",
    //         "mov" => "video",
    //         "flv" => "video",
    //         "swf" => "video",
    //         "mkv" => "video",
    //         "wmv" => "video",
    //         "wma" => "audio",
    //         "aac" => "audio",
    //         "wav" => "audio",
    //         "mp3" => "audio",
    //         "zip" => "archive",
    //         "rar" => "archive",
    //         "7z" => "archive",
    //         "doc" => "document",
    //         "txt" => "document",
    //         "docx" => "document",
    //         "pdf" => "document",
    //         "csv" => "document",
    //         "xml" => "document",
    //         "ods" => "document",
    //         "xlr" => "document",
    //         "xls" => "document",
    //         "xlsx" => "document"
    //     );

    //     if ($is_invoice == 1) {
    //         if ($request->hasFile('aiz_file')) {

    //             $upload = new Invoice_upload();
    //             $extension = strtolower($request->file('aiz_file')->getClientOriginalExtension());

    //             if (isset($type[$extension])) {
    //                 $upload->file_original_name = null;
    //                 $arr = explode('.', $request->file('aiz_file')->getClientOriginalName());
    //                 for ($i = 0; $i < count($arr) - 1; $i++) {
    //                     if ($i == 0) {
    //                         $upload->file_original_name .= $arr[$i];
    //                     } else {
    //                         $upload->file_original_name .= "." . $arr[$i];
    //                     }
    //                 }

    //                 if ($extension == 'svg') {
    //                     $sanitizer = new Sanitizer();
    //                     // Load the dirty svg
    //                     $dirtySVG = file_get_contents($request->file('aiz_file'));

    //                     // Pass it to the sanitizer and get it back clean
    //                     $cleanSVG = $sanitizer->sanitize($dirtySVG);

    //                     // Load the clean svg
    //                     file_put_contents($request->file('aiz_file'), $cleanSVG);
    //                 }

    //                 $path = $request->file('aiz_file')->store('uploads/invoice', 'local');
    //                 $size = $request->file('aiz_file')->getSize();

    //                 // Return MIME type ala mimetype extension
    //                 $finfo = finfo_open(FILEINFO_MIME_TYPE);

    //                 // Get the MIME type of the file
    //                 //$file_mime = finfo_file($finfo, base_path('public/') . $path);
    //                 $file_mime = Storage::mimeType($path);

    //                 if ($type[$extension] == 'image' && get_setting('disable_image_optimization') != 1) {
    //                     try {
    //                         $img = Image::make($request->file('aiz_file')->getRealPath())->encode();
    //                         $height = $img->height();
    //                         $width = $img->width();
    //                         if ($width > $height && $width > 1500) {
    //                             $img->resize(1500, null, function ($constraint) {
    //                                 $constraint->aspectRatio();
    //                             });
    //                         } elseif ($height > 1500) {
    //                             $img->resize(null, 800, function ($constraint) {
    //                                 $constraint->aspectRatio();
    //                             });
    //                         }
    //                         $img->save(base_path('public/') . $path);
    //                         clearstatcache();
    //                         $size = $img->filesize();
    //                     } catch (\Exception $e) {
    //                         //dd($e);
    //                     }
    //                 }

    //                 if (env('FILESYSTEM_DRIVER') == 's3') {
    //                     Storage::disk('s3')->put(
    //                         $path,
    //                         file_get_contents(base_path('public/') . $path),
    //                         [
    //                             'visibility' => 'public',
    //                             'ContentType' =>  $extension == 'svg' ? 'image/svg+xml' : $file_mime
    //                         ]
    //                     );
    //                     if ($arr[0] != 'updates') {
    //                         unlink(base_path('public/') . $path);
    //                     }
    //                 }

    //                 $upload->extension = $extension;
    //                 $upload->file_name = $path;
    //                 $upload->user_id = Auth::user()->id;
    //                 $upload->type = $type[$upload->extension];
    //                 $upload->file_size = $size;

    //                 // Set sales_file attribute based on route

    //                 $upload->sales_file = 0;

    //                 $upload->save();
    //             }
    //             return '{}';
    //         }

    //     } else if($is_invoice == 2) {

    //         if ($request->hasFile('aiz_file')) {

    //             $upload = new Invoice_upload();
    //             $extension = strtolower($request->file('aiz_file')->getClientOriginalExtension());

    //             if (isset($type[$extension])) {
    //                 $upload->file_original_name = null;
    //                 $arr = explode('.', $request->file('aiz_file')->getClientOriginalName());
    //                 for ($i = 0; $i < count($arr) - 1; $i++) {
    //                     if ($i == 0) {
    //                         $upload->file_original_name .= $arr[$i];
    //                     } else {
    //                         $upload->file_original_name .= "." . $arr[$i];
    //                     }
    //                 }

    //                 if ($extension == 'svg') {
    //                     $sanitizer = new Sanitizer();
    //                     // Load the dirty svg
    //                     $dirtySVG = file_get_contents($request->file('aiz_file'));

    //                     // Pass it to the sanitizer and get it back clean
    //                     $cleanSVG = $sanitizer->sanitize($dirtySVG);

    //                     // Load the clean svg
    //                     file_put_contents($request->file('aiz_file'), $cleanSVG);
    //                 }

    //                 $path = $request->file('aiz_file')->store('uploads/invoice', 'local');
    //                 $size = $request->file('aiz_file')->getSize();

    //                 // Return MIME type ala mimetype extension
    //                 $finfo = finfo_open(FILEINFO_MIME_TYPE);

    //                 // Get the MIME type of the file
    //                 //$file_mime = finfo_file($finfo, base_path('public/') . $path);
    //                 $file_mime = Storage::mimeType($path);

    //                 if ($type[$extension] == 'image' && get_setting('disable_image_optimization') != 1) {
    //                     try {
    //                         $img = Image::make($request->file('aiz_file')->getRealPath())->encode();
    //                         $height = $img->height();
    //                         $width = $img->width();
    //                         if ($width > $height && $width > 1500) {
    //                             $img->resize(1500, null, function ($constraint) {
    //                                 $constraint->aspectRatio();
    //                             });
    //                         } elseif ($height > 1500) {
    //                             $img->resize(null, 800, function ($constraint) {
    //                                 $constraint->aspectRatio();
    //                             });
    //                         }
    //                         $img->save(base_path('public/') . $path);
    //                         clearstatcache();
    //                         $size = $img->filesize();
    //                     } catch (\Exception $e) {
    //                         //dd($e);
    //                     }
    //                 }

    //                 if (env('FILESYSTEM_DRIVER') == 's3') {
    //                     Storage::disk('s3')->put(
    //                         $path,
    //                         file_get_contents(base_path('public/') . $path),
    //                         [
    //                             'visibility' => 'public',
    //                             'ContentType' =>  $extension == 'svg' ? 'image/svg+xml' : $file_mime
    //                         ]
    //                     );
    //                     if ($arr[0] != 'updates') {
    //                         unlink(base_path('public/') . $path);
    //                     }
    //                 }

    //                 $upload->extension = $extension;
    //                 $upload->file_name = $path;
    //                 $upload->user_id = Auth::user()->id;
    //                 $upload->type = $type[$upload->extension];
    //                 $upload->file_size = $size;

    //                 // Set sales_file attribute based on route

    //                 $upload->sales_file = 1;

    //                 $upload->save();
    //             }
    //             return '{}';
    //         }

    //     } else {

    //         if ($request->hasFile('aiz_file')) {
    //             $upload = new Upload;
    //             $extension = strtolower($request->file('aiz_file')->getClientOriginalExtension());
    //             if (isset($type[$extension])) {
    //                 $upload->file_original_name = null;
    //                 $arr = explode('.', $request->file('aiz_file')->getClientOriginalName());
    //                 for ($i = 0; $i < count($arr) - 1; $i++) {
    //                     if ($i == 0) {
    //                         $upload->file_original_name .= $arr[$i];
    //                     } else {
    //                         $upload->file_original_name .= "." . $arr[$i];
    //                     }
    //                 }

    //                 if ($extension == 'svg') {
    //                     $sanitizer = new Sanitizer();
    //                     // Load the dirty svg
    //                     $dirtySVG = file_get_contents($request->file('aiz_file'));

    //                     // Pass it to the sanitizer and get it back clean
    //                     $cleanSVG = $sanitizer->sanitize($dirtySVG);

    //                     // Load the clean svg
    //                     file_put_contents($request->file('aiz_file'), $cleanSVG);
    //                 }

    //                 $path = $request->file('aiz_file')->store('uploads/all', 'local');
    //                 $size = $request->file('aiz_file')->getSize();

    //                 // Return MIME type ala mimetype extension
    //                 $finfo = finfo_open(FILEINFO_MIME_TYPE);

    //                 // Get the MIME type of the file
    //                 //$file_mime = finfo_file($finfo, base_path('public/') . $path);
    //                 $file_mime = Storage::mimeType($path);

    //                 if ($type[$extension] == 'image' && get_setting('disable_image_optimization') != 1) {
    //                     try {
    //                         $img = Image::make($request->file('aiz_file')->getRealPath())->encode();
    //                         $height = $img->height();
    //                         $width = $img->width();
    //                         if ($width > $height && $width > 1500) {
    //                             $img->resize(1500, null, function ($constraint) {
    //                                 $constraint->aspectRatio();
    //                             });
    //                         } elseif ($height > 1500) {
    //                             $img->resize(null, 800, function ($constraint) {
    //                                 $constraint->aspectRatio();
    //                             });
    //                         }
    //                         $img->save(base_path('public/') . $path);
    //                         clearstatcache();
    //                         $size = $img->filesize();
    //                     } catch (\Exception $e) {
    //                         //dd($e);
    //                     }
    //                 }

    //                 if (env('FILESYSTEM_DRIVER') == 's3') {
    //                     Storage::disk('s3')->put(
    //                         $path,
    //                         file_get_contents(base_path('public/') . $path),
    //                         [
    //                             'visibility' => 'public',
    //                             'ContentType' =>  $extension == 'svg' ? 'image/svg+xml' : $file_mime
    //                         ]
    //                     );
    //                     if ($arr[0] != 'updates') {
    //                         unlink(base_path('public/') . $path);
    //                     }
    //                 }

    //                 $upload->extension = $extension;
    //                 $upload->file_name = $path;
    //                 $upload->user_id = Auth::user()->id;
    //                 $upload->type = $type[$upload->extension];
    //                 $upload->file_size = $size;
    //                 $upload->save();
    //             }
    //             return '{}';
    //         }
    //     }
    // }

    public function upload(Request $request, $is_invoice = 0)
    {
        $type = [
            "jpg" => "image",
            "jpeg" => "image",
            "png" => "image",
            "svg" => "image",
            "webp" => "image",
            "gif" => "image",
            "mp4" => "video",
            "mpg" => "video",
            "mpeg" => "video",
            "webm" => "video",
            "ogg" => "video",
            "avi" => "video",
            "mov" => "video",
            "flv" => "video",
            "swf" => "video",
            "mkv" => "video",
            "wmv" => "video",
            "wma" => "audio",
            "aac" => "audio",
            "wav" => "audio",
            "mp3" => "audio",
            "zip" => "archive",
            "rar" => "archive",
            "7z" => "archive",
            "doc" => "document",
            "txt" => "document",
            "docx" => "document",
            "pdf" => "document",
            "csv" => "document",
            "xml" => "document",
            "ods" => "document",
            "xlr" => "document",
            "xls" => "document",
            "xlsx" => "document"
        ];

        if ($request->hasFile('aiz_file')) {
            $extension = strtolower($request->file('aiz_file')->getClientOriginalExtension());

            if (!isset($type[$extension])) return '{}';

            $arr = explode('.', $request->file('aiz_file')->getClientOriginalName());
            $original_name = implode('.', array_slice($arr, 0, -1));

            if ($extension == 'svg') {
                $sanitizer = new Sanitizer();
                $dirtySVG = file_get_contents($request->file('aiz_file'));
                $cleanSVG = $sanitizer->sanitize($dirtySVG);
                file_put_contents($request->file('aiz_file'), $cleanSVG);
            }

            if ($is_invoice == 1 || $is_invoice == 2) {
                $upload = new Invoice_upload();
                $path = $request->file('aiz_file')->store('uploads/invoice', 'local');
                $size = $request->file('aiz_file')->getSize();
                $file_mime = Storage::mimeType($path);
            } else {
                // ✅ Save to public/uploads/all
                $upload = new Upload();
                $filename = Str::random(40) . '.' . $extension;
                $destinationPath = public_path('uploads/all');

                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0775, true);
                }

                $request->file('aiz_file')->move($destinationPath, $filename);
                $path = 'uploads/all/' . $filename;
                $fullPath = public_path($path);
                $size = filesize($fullPath);
                $file_mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $fullPath);
            }

            // Image optimization
            if ($type[$extension] == 'image' && get_setting('disable_image_optimization') != 1) {
                try {
                    $img = Image::make($is_invoice ? $request->file('aiz_file')->getRealPath() : $fullPath)->encode();
                    $height = $img->height();
                    $width = $img->width();
                    if ($width > $height && $width > 1500) {
                        $img->resize(1500, null, fn($constraint) => $constraint->aspectRatio());
                    } elseif ($height > 1500) {
                        $img->resize(null, 800, fn($constraint) => $constraint->aspectRatio());
                    }
                    $img->save($is_invoice ? base_path('public/') . $path : $fullPath);
                    clearstatcache();
                    $size = $img->filesize();
                } catch (\Exception $e) {
                    // Optionally log the error
                }
            }

            // S3 upload
            if (env('FILESYSTEM_DRIVER') == 's3') {
                $upload_path = $is_invoice ? base_path('public/') . $path : $fullPath;

                Storage::disk('s3')->put(
                    $path,
                    file_get_contents($upload_path),
                    [
                        'visibility' => 'public',
                        'ContentType' => $extension == 'svg' ? 'image/svg+xml' : $file_mime
                    ]
                );

                if ($arr[0] != 'updates') {
                    unlink($upload_path);
                }
            }

            $upload->extension = $extension;
            $upload->file_original_name = $original_name;
            $upload->file_name = $path;
            $upload->user_id = Auth::user()->id;
            $upload->type = $type[$extension];
            $upload->file_size = $size;

            if ($is_invoice == 1) {
                $upload->sales_file = 0;
            } elseif ($is_invoice == 2) {
                $upload->sales_file = 1;
            }

            $upload->save();
            return '{}';
        }

        return '{}';
    }




    public function uploaded_invoice(Request $request)
    {
        $query = Invoice_upload::query()->where('sales_file', 0);

        $search = $request->search;
        $sort_by = $request->sort;

        if ($search != null) {
            $query->where('file_original_name', 'like', '%' . $search . '%');
        }

        switch ($sort_by) {
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'smallest':
                $query->orderBy('file_size', 'asc');
                break;
            case 'largest':
                $query->orderBy('file_size', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $all_uploads = $query->paginate(60)->appends(request()->query());
        return view('backend.uploaded_invoice.index', compact('all_uploads', 'search', 'sort_by'));
    }

    public function uploaded_sales_invoice(Request $request)
    {
        $query = Invoice_upload::query()->where('sales_file', 1);

        $search = $request->search;
        $sort_by = $request->sort;

        if ($search != null) {
            $query->where('file_original_name', 'like', '%' . $search . '%');
        }

        switch ($sort_by) {
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'smallest':
                $query->orderBy('file_size', 'asc');
                break;
            case 'largest':
                $query->orderBy('file_size', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $all_uploads = $query->paginate(60)->appends(request()->query());
        return view('backend.uploaded_invoice.sales.index', compact('all_uploads', 'search', 'sort_by'));
    }


    public function get_uploaded_files(Request $request)
    {
        $uploads = Upload::where('user_id', Auth::user()->id)->where('invoice_file', 0);
        if ($request->search != null) {
            $uploads->where('file_original_name', 'like', '%' . $request->search . '%');
        }
        if ($request->sort != null) {
            switch ($request->sort) {
                case 'newest':
                    $uploads->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $uploads->orderBy('created_at', 'asc');
                    break;
                case 'smallest':
                    $uploads->orderBy('file_size', 'asc');
                    break;
                case 'largest':
                    $uploads->orderBy('file_size', 'desc');
                    break;
                default:
                    $uploads->orderBy('created_at', 'desc');
                    break;
            }
        }
        return $uploads->paginate(60)->appends(request()->query());
    }

    public function destroy($id)
    {
        $upload = Upload::findOrFail($id);

        if (auth()->user()->user_type == 'seller' && $upload->user_id != auth()->user()->id) {
            flash(translate("You don't have permission for deleting this!"))->error();
            return back();
        }
        try {
            if (env('FILESYSTEM_DRIVER') == 's3') {
                Storage::disk('s3')->delete($upload->file_name);
                if (file_exists(public_path() . '/' . $upload->file_name)) {
                    unlink(public_path() . '/' . $upload->file_name);
                }
            } else {
                unlink(public_path() . '/' . $upload->file_name);
            }
            $upload->delete();
            flash(translate('File deleted successfully'))->success();
        } catch (\Exception $e) {
            $upload->delete();
            flash(translate('File deleted successfully'))->success();
        }
        return back();
    }

    public function bulk_uploaded_files_delete(Request $request)
    {
        if ($request->id) {
            foreach ($request->id as $file_id) {
                $this->destroy($file_id);
            }
            return 1;
        } else {
            return 0;
        }
    }

    public function get_preview_files(Request $request)
    {
        $ids = explode(',', $request->ids);
        $files = Upload::whereIn('id', $ids)->get()->where('invoice_file', 0);
        $new_file_array = [];
        foreach ($files as $file) {
            $file['file_name'] = $file->file_name;
            if ($file->external_link) {
                $file['file_name'] = $file->external_link;
            }
            $new_file_array[] = $file;
        }
        //  dd($new_file_array);
        return $new_file_array;
        // return $files;
    }

    public function all_file()
    {
        $uploads = Upload::all()->where('invoice_file', 0);
        foreach ($uploads as $upload) {
            try {
                if (env('FILESYSTEM_DRIVER') == 's3') {
                    Storage::disk('s3')->delete($upload->file_name);
                    if (file_exists(public_path() . '/' . $upload->file_name)) {
                        unlink(public_path() . '/' . $upload->file_name);
                    }
                } else {
                    unlink(public_path() . '/' . $upload->file_name);
                }
                $upload->delete();
                flash(translate('File deleted successfully'))->success();
            } catch (\Exception $e) {
                $upload->delete();
                flash(translate('File deleted successfully'))->success();
            }
        }

        Upload::query()->truncate();

        return back();
    }

    //Download project attachment
    public function attachment_download($id)
    {
        $project_attachment = Upload::find($id)->where('invoice_file', 0);
        try {
            $file_path = public_path($project_attachment->file_name);
            return Response::download($file_path);
        } catch (\Exception $e) {
            flash(translate('File does not exist!'))->error();
            return back();
        }
    }
    //Download project attachment
    public function file_info(Request $request)
    {
        $file = Upload::findOrFail($request['id'])->where('invoice_file', 0);

        return (auth()->user()->user_type == 'seller')
            ? view('seller.uploads.info', compact('file'))
            : view('backend.uploaded_files.info', compact('file'));
    }
}
