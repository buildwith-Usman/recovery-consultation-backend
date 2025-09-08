<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class EmailPreviewController extends Controller
{
    public function verificationEmail()
    {
        // Create a sample user for preview
        $user = new User([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'type' => 'patient'
        ]);
        
        $verificationCode = '12345';
        
        return view('emails.verification', [
            'user' => $user,
            'verificationCode' => $verificationCode
        ]);
    }
}
