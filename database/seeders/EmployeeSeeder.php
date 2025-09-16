<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Company;
use App\Models\Site;
use App\Models\EmployeeSiteAllocation;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $companies = Company::all();

        foreach ($companies as $companyIndex => $company) {
            // Get sites for this company
            $sites = Site::where('company_id', $company->id)->get();

            // Create sample employees for each company
            $employees = [
                [
                    'employee_id' => 'EMP-' . str_pad($companyIndex + 1, 2, '0', STR_PAD_LEFT) . '01',
                    'first_name' => 'John',
                    'last_name' => 'Mitchell',
                    'email' => 'j.mitchell@' . strtolower(str_replace(' ', '', $company->name)) . '.com',
                    'phone' => '+1-555-01' . str_pad($companyIndex + 1, 2, '0', STR_PAD_LEFT),
                    'role' => Employee::ROLE_SITE_MANAGER,
                    'department' => 'Operations',
                    'job_title' => 'Senior Site Manager',
                    'hire_date' => now()->subYears(3)->format('Y-m-d'),
                    'employment_status' => Employee::STATUS_ACTIVE,
                    'employment_type' => 'full_time',
                    'salary' => 85000,
                    'salary_type' => 'yearly',
                    'skills' => ['Project Management', 'Team Leadership', 'Safety Management', 'Quality Control'],
                    'certifications' => ['PMP', 'OSHA 30', 'First Aid/CPR'],
                    'qualifications' => ['Bachelor\'s in Construction Management', '10+ years experience'],
                ],
                [
                    'employee_id' => 'EMP-' . str_pad($companyIndex + 1, 2, '0', STR_PAD_LEFT) . '02',
                    'first_name' => 'Sarah',
                    'last_name' => 'Thompson',
                    'email' => 's.thompson@' . strtolower(str_replace(' ', '', $company->name)) . '.com',
                    'phone' => '+1-555-02' . str_pad($companyIndex + 1, 2, '0', STR_PAD_LEFT),
                    'role' => Employee::ROLE_QUANTITY_SURVEYOR,
                    'department' => 'Estimating',
                    'job_title' => 'Senior Quantity Surveyor',
                    'hire_date' => now()->subYears(2)->format('Y-m-d'),
                    'employment_status' => Employee::STATUS_ACTIVE,
                    'employment_type' => 'full_time',
                    'salary' => 75000,
                    'salary_type' => 'yearly',
                    'skills' => ['Cost Estimation', 'Material Takeoff', 'Contract Analysis', 'Risk Assessment'],
                    'certifications' => ['RICS', 'AACE International'],
                    'qualifications' => ['Bachelor\'s in Quantity Surveying', 'RICS Chartered Surveyor'],
                ],
                [
                    'employee_id' => 'EMP-' . str_pad($companyIndex + 1, 2, '0', STR_PAD_LEFT) . '03',
                    'first_name' => 'Michael',
                    'last_name' => 'Rodriguez',
                    'email' => 'm.rodriguez@' . strtolower(str_replace(' ', '', $company->name)) . '.com',
                    'phone' => '+1-555-03' . str_pad($companyIndex + 1, 2, '0', STR_PAD_LEFT),
                    'role' => Employee::ROLE_CONTRACT_MANAGER,
                    'department' => 'Legal & Contracts',
                    'job_title' => 'Contract Manager',
                    'hire_date' => now()->subYears(4)->format('Y-m-d'),
                    'employment_status' => Employee::STATUS_ACTIVE,
                    'employment_type' => 'full_time',
                    'salary' => 78000,
                    'salary_type' => 'yearly',
                    'skills' => ['Contract Negotiation', 'Legal Compliance', 'Risk Management', 'Vendor Relations'],
                    'certifications' => ['CCCM', 'Legal Studies Certificate'],
                    'qualifications' => ['Bachelor\'s in Business Administration', 'Paralegal Certificate'],
                ],
                [
                    'employee_id' => 'EMP-' . str_pad($companyIndex + 1, 2, '0', STR_PAD_LEFT) . '04',
                    'first_name' => 'Emily',
                    'last_name' => 'Chen',
                    'email' => 'e.chen@' . strtolower(str_replace(' ', '', $company->name)) . '.com',
                    'phone' => '+1-555-04' . str_pad($companyIndex + 1, 2, '0', STR_PAD_LEFT),
                    'role' => Employee::ROLE_SAFETY_OFFICER,
                    'department' => 'Health & Safety',
                    'job_title' => 'Safety Coordinator',
                    'hire_date' => now()->subYears(1)->format('Y-m-d'),
                    'employment_status' => Employee::STATUS_ACTIVE,
                    'employment_type' => 'full_time',
                    'salary' => 65000,
                    'salary_type' => 'yearly',
                    'skills' => ['Safety Inspections', 'Incident Investigation', 'Training Development', 'Regulatory Compliance'],
                    'certifications' => ['OSHA 30', 'CSP', 'First Aid/CPR', 'Confined Space'],
                    'qualifications' => ['Bachelor\'s in Occupational Safety', 'NEBOSH General Certificate'],
                ],
                [
                    'employee_id' => 'EMP-' . str_pad($companyIndex + 1, 2, '0', STR_PAD_LEFT) . '05',
                    'first_name' => 'David',
                    'last_name' => 'Wilson',
                    'email' => 'd.wilson@' . strtolower(str_replace(' ', '', $company->name)) . '.com',
                    'phone' => '+1-555-05' . str_pad($companyIndex + 1, 2, '0', STR_PAD_LEFT),
                    'role' => Employee::ROLE_ARCHITECT,
                    'department' => 'Design',
                    'job_title' => 'Project Architect',
                    'hire_date' => now()->subYears(5)->format('Y-m-d'),
                    'employment_status' => Employee::STATUS_ACTIVE,
                    'employment_type' => 'full_time',
                    'salary' => 82000,
                    'salary_type' => 'yearly',
                    'skills' => ['AutoCAD', 'Revit', 'SketchUp', 'Building Codes', 'Sustainable Design'],
                    'certifications' => ['AIA', 'LEED AP', 'NCARB'],
                    'qualifications' => ['Master of Architecture', 'Licensed Architect'],
                ],
                [
                    'employee_id' => 'EMP-' . str_pad($companyIndex + 1, 2, '0', STR_PAD_LEFT) . '06',
                    'first_name' => 'Lisa',
                    'last_name' => 'Anderson',
                    'email' => 'l.anderson@' . strtolower(str_replace(' ', '', $company->name)) . '.com',
                    'phone' => '+1-555-06' . str_pad($companyIndex + 1, 2, '0', STR_PAD_LEFT),
                    'role' => Employee::ROLE_ENGINEER,
                    'department' => 'Engineering',
                    'job_title' => 'Structural Engineer',
                    'hire_date' => now()->subYears(2)->format('Y-m-d'),
                    'employment_status' => Employee::STATUS_ACTIVE,
                    'employment_type' => 'full_time',
                    'salary' => 88000,
                    'salary_type' => 'yearly',
                    'skills' => ['Structural Analysis', 'Steel Design', 'Concrete Design', 'Seismic Design'],
                    'certifications' => ['PE License', 'SE License'],
                    'qualifications' => ['Master\'s in Structural Engineering', 'Professional Engineer'],
                ],
                [
                    'employee_id' => 'EMP-' . str_pad($companyIndex + 1, 2, '0', STR_PAD_LEFT) . '07',
                    'first_name' => 'Robert',
                    'last_name' => 'Taylor',
                    'email' => 'r.taylor@' . strtolower(str_replace(' ', '', $company->name)) . '.com',
                    'phone' => '+1-555-07' . str_pad($companyIndex + 1, 2, '0', STR_PAD_LEFT),
                    'role' => Employee::ROLE_FOREMAN,
                    'department' => 'Field Operations',
                    'job_title' => 'Construction Foreman',
                    'hire_date' => now()->subYears(6)->format('Y-m-d'),
                    'employment_status' => Employee::STATUS_ACTIVE,
                    'employment_type' => 'full_time',
                    'salary' => 32.50,
                    'salary_type' => 'hourly',
                    'skills' => ['Crew Management', 'Quality Control', 'Equipment Operation', 'Blueprint Reading'],
                    'certifications' => ['OSHA 10', 'Crane Operator', 'Forklift Operator'],
                    'qualifications' => ['Trade School Certificate', '15+ years field experience'],
                ],
                [
                    'employee_id' => 'EMP-' . str_pad($companyIndex + 1, 2, '0', STR_PAD_LEFT) . '08',
                    'first_name' => 'Jennifer',
                    'last_name' => 'Brown',
                    'email' => 'j.brown@' . strtolower(str_replace(' ', '', $company->name)) . '.com',
                    'phone' => '+1-555-08' . str_pad($companyIndex + 1, 2, '0', STR_PAD_LEFT),
                    'role' => Employee::ROLE_PROCUREMENT_MANAGER,
                    'department' => 'Procurement',
                    'job_title' => 'Procurement Specialist',
                    'hire_date' => now()->subMonths(8)->format('Y-m-d'),
                    'employment_status' => Employee::STATUS_ACTIVE,
                    'employment_type' => 'full_time',
                    'salary' => 70000,
                    'salary_type' => 'yearly',
                    'skills' => ['Vendor Management', 'Cost Analysis', 'Supply Chain', 'Negotiation'],
                    'certifications' => ['CPSM', 'APICS'],
                    'qualifications' => ['Bachelor\'s in Supply Chain Management'],
                ],
            ];

            foreach ($employees as $employeeData) {
                $employee = Employee::create(array_merge($employeeData, [
                    'company_id' => $company->id,
                    'address' => fake()->streetAddress(),
                    'city' => fake()->city(),
                    'state' => fake()->state(),
                    'zip_code' => fake()->postcode(),
                    'country' => 'US',
                    'date_of_birth' => fake()->dateTimeBetween('-60 years', '-25 years')->format('Y-m-d'),
                    'gender' => fake()->randomElement(['male', 'female']),
                    'emergency_contact_name' => fake()->name(),
                    'emergency_contact_phone' => fake()->phoneNumber(),
                    'emergency_contact_relationship' => fake()->randomElement(['Spouse', 'Parent', 'Sibling', 'Child']),
                    'notes' => fake()->optional(0.3)->sentence(),
                ]));

                // Allocate some employees to sites
                if ($sites->count() > 0 && fake()->boolean(70)) {
                    $randomSites = $sites->random(min(2, $sites->count()));
                    
                    foreach ($randomSites as $index => $site) {
                        EmployeeSiteAllocation::create([
                            'employee_id' => $employee->id,
                            'site_id' => $site->id,
                            'allocated_from' => now()->subDays(fake()->numberBetween(30, 365))->format('Y-m-d'),
                            'allocated_until' => fake()->boolean(60) ? now()->addDays(fake()->numberBetween(30, 730))->format('Y-m-d') : null,
                            'allocation_type' => $index === 0 ? 'primary' : fake()->randomElement(['secondary', 'temporary']),
                            'allocation_percentage' => $index === 0 ? 100 : fake()->numberBetween(25, 75),
                            'responsibilities' => fake()->optional(0.7)->sentence(),
                            'status' => 'active',
                        ]);
                    }
                }
            }
        }
    }
}
