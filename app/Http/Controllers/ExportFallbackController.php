<?php

namespace App\Http\Controllers;

use App\Models\StakeholderCommunication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class ExportFallbackController extends Controller
{
    /**
     * Export stakeholder communications as a CSV (fallback method)
     */
    public function exportCsv(Request $request)
    {
        try {
            $query = StakeholderCommunication::with(['stakeholder', 'users']);

            if ($request->filled('start_date')) {
                $query->whereDate('meeting_date', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('meeting_date', '<=', $request->end_date);
            }

            $communications = $query->get();

            // Create the filename
            $filename = 'stakeholder-communications-' . now()->format('Y-m-d');
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $filename = 'stakeholder-communications-' . $request->start_date . '-to-' . $request->end_date;
            }
            $filename = preg_replace('/[^a-z0-9-_]/i', '-', $filename) . '.csv';

            // Create CSV content
            $headers = [
                'ID',
                'Stakeholder',
                'Meeting Date',
                'Meeting Time',
                'Meeting Type',
                'Location',
                'Attendees',
                'Discussion Points',
                'Action Items',
                'Follow Up Notes',
                'Follow Up Date',
                'Assigned Users',
                'Created At'
            ];

            $callback = function() use ($communications, $headers) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $headers);

                foreach ($communications as $communication) {
                    try {
                        // Clean text function
                        $cleanText = function($text) {
                            if (empty($text)) return '';
                            $text = (string) $text;
                            $text = strip_tags($text);
                            $text = preg_replace('/[\r\n\t\f\v\0-\x1F\x7F]/', ' ', $text);
                            $text = preg_replace('/[^\x20-\x7E]/', '', $text);
                            $text = trim($text);
                            $text = preg_replace('/\s+/', ' ', $text);
                            return $text;
                        };

                        // Format meeting time
                        $formattedTime = 'N/A';
                        try {
                            if (!empty($communication->meeting_time)) {
                                if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $communication->meeting_time)) {
                                    $meetingTime = \Carbon\Carbon::createFromFormat('H:i:s', $communication->meeting_time);
                                    $formattedTime = $meetingTime->format('h:i A');
                                } elseif (preg_match('/^\d{2}:\d{2}$/', $communication->meeting_time)) {
                                    $meetingTime = \Carbon\Carbon::createFromFormat('H:i', $communication->meeting_time);
                                    $formattedTime = $meetingTime->format('h:i A');
                                } else {
                                    $formattedTime = $communication->meeting_time;
                                }
                            }
                        } catch (\Exception $e) {
                            $formattedTime = $communication->meeting_time ?? 'N/A';
                        }

                        // Get stakeholder name
                        $stakeholderName = '';
                        if (isset($communication->stakeholder) && isset($communication->stakeholder->name)) {
                            $stakeholderName = $communication->stakeholder->name;
                        }

                        // Format dates
                        $meetingDate = '';
                        try {
                            if (!empty($communication->meeting_date)) {
                                $meetingDate = $communication->meeting_date->format('Y-m-d');
                            }
                        } catch (\Exception $e) {
                            $meetingDate = 'Invalid Date';
                        }

                        $followUpDate = '';
                        try {
                            if (!empty($communication->follow_up_date)) {
                                $followUpDate = $communication->follow_up_date->format('Y-m-d');
                            }
                        } catch (\Exception $e) {
                            $followUpDate = 'Invalid Date';
                        }

                        $createdAt = '';
                        try {
                            if (!empty($communication->created_at)) {
                                $createdAt = $communication->created_at->format('Y-m-d h:i A');
                            }
                        } catch (\Exception $e) {
                            $createdAt = 'Invalid Date';
                        }

                        // Get users
                        $usersString = '';
                        try {
                            if (isset($communication->users) && $communication->users->count() > 0) {
                                $usersString = $communication->users->pluck('name')->implode(', ');
                            }
                        } catch (\Exception $e) {
                            $usersString = 'Error retrieving users';
                        }

                        $row = [
                            $communication->id ?? 'N/A',
                            $cleanText($stakeholderName),
                            $meetingDate,
                            $formattedTime,
                            $cleanText(ucfirst($communication->meeting_type ?? '')),
                            $cleanText($communication->location ?? ''),
                            $cleanText($communication->attendees ?? ''),
                            $cleanText($communication->discussion_points ?? ''),
                            $cleanText($communication->action_items ?? ''),
                            $cleanText($communication->follow_up_notes ?? ''),
                            $followUpDate,
                            $cleanText($usersString),
                            $createdAt
                        ];

                        fputcsv($file, $row);
                    } catch (\Exception $rowException) {
                        // Log error but continue with other rows
                        Log::error('Error processing row in CSV export: ' . $rowException->getMessage(), [
                            'communication_id' => $communication->id ?? 'unknown'
                        ]);
                    }
                }

                fclose($file);
            };

            return Response::stream($callback, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        } catch (\Exception $e) {
            Log::error('CSV Export failed: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'CSV Export failed: ' . $e->getMessage());
        }
    }
}
