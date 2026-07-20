<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    // ── API: receive form submission from the React site ──────────────────
    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'mobile'  => 'nullable|string|max:30',
            'website' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);

        $contact = Contact::create([
            'name'    => $request->name,
            'email'   => $request->email,
            'mobile'  => $request->mobile,
            'website' => $request->website,
            'message' => $request->message,
            'status'  => 'unread',
        ]);

        return response()->json([
            'message' => 'Message received. Thank you for contacting us!',
            'id'      => $contact->id,
        ], 201);
    }

    // ── Dashboard: list all contact messages ──────────────────────────────
    public function manage()
    {
        $contacts = Contact::latest()->paginate(20);
        $stats = [
            'total'  => Contact::count(),
            'unread' => Contact::where('status', 'unread')->count(),
            'read'   => Contact::where('status', 'read')->count(),
        ];

        return view('contact.manageContacts', compact('contacts', 'stats'));
    }

    // ── Dashboard: mark a message as read ────────────────────────────────
    public function markRead(Contact $contact)
    {
        $contact->update(['status' => 'read']);

        return response()->json(['success' => true, 'message' => 'Marked as read']);
    }

    // ── Dashboard: delete a message ───────────────────────────────────────
    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()->route('manage.contacts')
                         ->with('success', 'Message deleted successfully.');
    }
}