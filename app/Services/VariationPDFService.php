<?php

namespace App\Services;

use TCPDF;

class VariationPDFService
{
    public function generateVariationPDF($variation, $project, $client, $company)
    {
        // Create new PDF document
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator('GoBillo');
        $pdf->SetAuthor($company->name);
        $pdf->SetTitle('Project Variation - ' . $variation->variation_number);
        $pdf->SetSubject('Project Variation Document');

        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Set margins
        $pdf->SetMargins(20, 20, 20);
        $pdf->SetAutoPageBreak(TRUE, 25);

        // Add a page
        $pdf->AddPage();

        // Set font
        $pdf->SetFont('helvetica', '', 12);

        // Company Header
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->SetTextColor(0, 123, 255); // Bootstrap primary blue
        $pdf->Cell(0, 10, $company->name, 0, 1, 'R');
        
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(0, 0, 0);
        if ($company->address) {
            $pdf->Cell(0, 5, $company->address, 0, 1, 'R');
        }
        if ($company->phone) {
            $pdf->Cell(0, 5, 'Tel: ' . $company->phone, 0, 1, 'R');
        }
        if ($company->email) {
            $pdf->Cell(0, 5, 'Email: ' . $company->email, 0, 1, 'R');
        }

        // Title
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'B', 24);
        $pdf->SetTextColor(0, 123, 255);
        $pdf->Cell(0, 15, 'PROJECT VARIATION', 0, 1, 'L');
        
        $pdf->SetFont('helvetica', '', 16);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(0, 8, $variation->variation_number, 0, 1, 'L');

        // Separator line
        $pdf->Ln(5);
        $pdf->SetDrawColor(0, 123, 255);
        $pdf->Line(20, $pdf->GetY(), 190, $pdf->GetY());
        $pdf->Ln(10);

        // Client Information
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 8, 'CLIENT INFORMATION', 0, 1, 'L');
        
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(40, 6, 'Company:', 0, 0, 'L');
        $pdf->Cell(0, 6, $client->company_name, 0, 1, 'L');
        
        if ($client->contact_name && $client->contact_name !== $client->company_name) {
            $pdf->Cell(40, 6, 'Contact:', 0, 0, 'L');
            $pdf->Cell(0, 6, $client->contact_name, 0, 1, 'L');
        }
        
        $pdf->Cell(40, 6, 'Email:', 0, 0, 'L');
        $pdf->Cell(0, 6, $client->email, 0, 1, 'L');

        $pdf->Ln(8);

        // Project Information
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 8, 'PROJECT INFORMATION', 0, 1, 'L');
        
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(40, 6, 'Project Name:', 0, 0, 'L');
        $pdf->Cell(0, 6, $project->name, 0, 1, 'L');
        
        if ($project->description) {
            $pdf->Cell(40, 6, 'Description:', 0, 0, 'L');
            $pdf->MultiCell(0, 6, $project->description, 0, 'L');
        }
        
        $pdf->Cell(40, 6, 'Status:', 0, 0, 'L');
        $pdf->Cell(0, 6, ucfirst(str_replace('_', ' ', $project->status)), 0, 1, 'L');

        $pdf->Ln(8);

        // Variation Details
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 8, 'VARIATION DETAILS', 0, 1, 'L');
        
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(40, 6, 'Title:', 0, 0, 'L');
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 6, $variation->title, 0, 1, 'L');
        
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(40, 6, 'Type:', 0, 0, 'L');
        $pdf->Cell(0, 6, ucfirst(str_replace('_', ' ', $variation->type)), 0, 1, 'L');
        
        $pdf->Cell(40, 6, 'Status:', 0, 0, 'L');
        $pdf->Cell(0, 6, ucfirst(str_replace('_', ' ', $variation->status)), 0, 1, 'L');
        
        $pdf->Cell(40, 6, 'Requested:', 0, 0, 'L');
        $pdf->Cell(0, 6, $variation->requested_date->format('F j, Y'), 0, 1, 'L');
        
        if ($variation->required_by_date) {
            $pdf->Cell(40, 6, 'Required By:', 0, 0, 'L');
            $pdf->Cell(0, 6, $variation->required_by_date->format('F j, Y'), 0, 1, 'L');
        }
        
        if ($variation->client_reference) {
            $pdf->Cell(40, 6, 'Your Reference:', 0, 0, 'L');
            $pdf->Cell(0, 6, $variation->client_reference, 0, 1, 'L');
        }

        $pdf->Ln(5);

        // Impact Summary Box
        $pdf->SetFillColor(248, 249, 250); // Light gray background
        $pdf->SetDrawColor(200, 200, 200);
        $pdf->Rect(20, $pdf->GetY(), 170, 20, 'DF');
        
        $pdf->Ln(3);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(85, 6, 'Cost Impact:', 0, 0, 'L');
        
        // Color code the cost impact
        if ($variation->cost_impact >= 0) {
            $pdf->SetTextColor(40, 167, 69); // Green for positive
        } else {
            $pdf->SetTextColor(220, 53, 69); // Red for negative
        }
        $pdf->Cell(85, 6, $variation->formatted_cost_impact, 0, 1, 'L');
        
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(85, 6, 'Time Impact:', 0, 0, 'L');
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(85, 6, $variation->formatted_time_impact, 0, 1, 'L');

        $pdf->Ln(8);

        // Description
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 8, 'DESCRIPTION', 0, 1, 'L');
        
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetDrawColor(0, 123, 255);
        $pdf->Rect(20, $pdf->GetY(), 170, 0, 'D'); // Just a line
        
        $pdf->Ln(2);
        $pdf->SetFont('helvetica', '', 11);
        $pdf->MultiCell(0, 6, $variation->description, 0, 'L');

        $pdf->Ln(5);

        // Reason
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 8, 'REASON FOR VARIATION', 0, 1, 'L');
        
        $pdf->SetDrawColor(0, 123, 255);
        $pdf->Rect(20, $pdf->GetY(), 170, 0, 'D'); // Just a line
        
        $pdf->Ln(2);
        $pdf->SetFont('helvetica', '', 11);
        $pdf->MultiCell(0, 6, $variation->reason, 0, 'L');

        // Approval Notes (if any)
        if ($variation->approval_notes) {
            $pdf->Ln(5);
            $pdf->SetFont('helvetica', 'B', 14);
            $pdf->Cell(0, 8, 'APPROVAL NOTES', 0, 1, 'L');
            
            $pdf->SetDrawColor(0, 123, 255);
            $pdf->Rect(20, $pdf->GetY(), 170, 0, 'D'); // Just a line
            
            $pdf->Ln(2);
            $pdf->SetFont('helvetica', '', 11);
            $pdf->MultiCell(0, 6, $variation->approval_notes, 0, 'L');
        }

        // Footer
        $pdf->Ln(15);
        $pdf->SetDrawColor(200, 200, 200);
        $pdf->Line(20, $pdf->GetY(), 190, $pdf->GetY());
        $pdf->Ln(3);
        
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(0, 5, 'This variation document was generated on ' . now()->format('F j, Y \a\t g:i A'), 0, 1, 'L');
        $pdf->Cell(0, 5, 'Please review this variation and contact us with any questions or to proceed with approval.', 0, 1, 'L');

        // Return PDF content
        return $pdf->Output('', 'S'); // 'S' returns the PDF as a string
    }
}
