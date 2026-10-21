# Uzazi Clinic — System Specification

> **Codename:** `drissa` · Hospital & Clinic Management System
>
> **This document is technology-agnostic.** It describes *what* the system does — its users, screens, workflows, data, rules and integrations — not *how* it is built. Use it as the source of truth to implement the system on any stack.

---

## Table of Contents

1. [System Overview](#1-system-overview)
2. [System Map](#2-system-map)
3. [User Roles & Access Control](#3-user-roles--access-control)
4. [Access Matrix](#4-access-matrix)
5. [Patient Visit Workflow (State Machine)](#5-patient-visit-workflow-state-machine)
6. [Modules, Pages & Flows](#6-modules-pages--flows)
7. [Data Entities](#7-data-entities)
8. [Business Rules](#8-business-rules)
9. [SMS & Notifications](#9-sms--notifications)
10. [Default Data & Credentials](#10-default-data--credentials)
11. [First-Run Setup](#11-first-run-setup)
12. [Security & Audit Requirements](#12-security--audit-requirements)

---

## 1. System Overview

Uzazi Clinic is a full-cycle clinic management system covering the entire patient journey:

**Registration → Triage/Vitals → Doctor Consultation → Lab / Ultrasound → Pharmacy → Billing → Discharge**

It includes a public-facing website (home, about, branches, services, blog, shop, appointment booking, contact) and a staff portal with role-based workspaces, team chat, SMS/email templating, inventory management, invoicing with PDF export, and reporting/analytics.

The system is bilingual in practice — staff UI is English-first while patient-facing SMS messages mix Swahili and English. Currency is Tanzanian Shilling (TSh); default country context is Tanzania (+255 phone numbers).

---

## 2. System Map

```
┌─────────────────────────────────────────────────────────────────┐
│                         PUBLIC WEBSITE                           │
│   Home · About · Branches · Services · Book Appointment          │
│   Blog · Shop · Contact                 (no login required)      │
└─────────────────────────────────────────────────────────────────┘
                              │
┌─────────────────────────────────────────────────────────────────┐
│                            LOGIN                                 │
│   Email + password · self-registration disabled                  │
│   Password reset via email                                       │
└─────────────────────────────────────────────────────────────────┘
                              │
┌─────────────────────────────────────────────────────────────────┐
│                       ACCESS CONTROL                             │
│   Every staff page requires login.                               │
│   Wrong role on a department page → soft redirect to the user's  │
│   own workspace (never a dead end).                              │
│   Wrong role on an admin page → hard "access denied".            │
│   Admin can open every department's workspace.                   │
└─────────────────────────────────────────────────────────────────┘
                              │
   ┌────────────┬────────────┬───────────┬───────────┬────────────┐
   ▼            ▼            ▼           ▼           ▼            ▼
┌─────────┐┌──────────┐┌─────────┐┌─────────┐┌──────────┐┌────────┐
│Reception││  Doctor  ││  Nurse  ││   Lab   ││ Pharmacy ││Ultra-  │
│Workspace││Workspace ││Workspace││Workspace││Workspace ││sound   │
└─────────┘└──────────┘└─────────┘└─────────┘└──────────┘└────────┘
                              │
                        ┌─────▼──────────┐
                        │   ADMIN PANEL   │
                        │ users·catalogs  │
                        │ reports·logs    │
                        │ settings·SMS    │
                        └─────────────────┘
```

Each role lands on its own workspace after login. Shared features — patient registry, clinical records, team chat, SMS composer, profile — are reachable from every workspace.

---

## 3. User Roles & Access Control

Seven staff roles exist. Each staff account carries exactly one role; **admin** implicitly has access to everything.

| Role | Workspace | Purpose |
|---|---|---|
| **admin** | Main dashboard + every module | Full access: users, doctors, catalogs, settings, reports, audit logs, notifications |
| **reception** | Reception workspace | Register patients, open visits, assign/change doctors, take payments, manage invoices |
| **doctor** | Doctor workspace | Call patients, consultations, vitals, lab/ultrasound orders, prescriptions, complete visits |
| **nurse** | Nurse workspace | Queue view, record vitals, patient list |
| **lab** | Lab workspace | Lab order queue, processing, result entry, test catalog & equipment registry |
| **pharmacy** | Pharmacy workspace | Prescription queue, dispensing, medication inventory, pharmacy reports |
| **ultrasound** | Ultrasound workspace | Ultrasound order queue, scan results, service catalog |

### Access control rules

- **Soft redirect** — a staff member opening another department's page is redirected to *their own* workspace with an informational message. No dead ends, no error pages for honest mistakes.
- **Hard deny** — admin-only areas (reports, activity logs, notifications panel, user & doctor management, settings) show "access denied" to non-admins.
- **Admin override** — admin can open any department workspace and act there (view all queues, discharge visits, complete lab/payment steps).
- **Shared pages** — patient registry, clinical records, team chat, SMS composer and profile are open to every authenticated staff role.

---

## 4. Access Matrix

What each role can reach:

| Area / Route | admin | reception | doctor | nurse | lab | pharmacy | ultrasound |
|---|:-:|:-:|:-:|:-:|:-:|:-:|:-:|
| `/dashboard`, stats | ✅ | ✅ | ↩ | ↩ | ↩ | ↩ | ↩ |
| `/reception/*` (queue, payments, visits) | ✅ | ✅ | — | — | — | — | — |
| `/doctor/*` (queue, consult, orders, prescribe) | ✅ | — | ✅ | — | — | — | — |
| `/lab/*` + `lab-equipment`, `lab-tests` CRUD | ✅ | — | — | — | ✅ | — | — |
| `/ultrasound/*` + `ultrasound-services` CRUD | ✅ | — | — | — | ✅ | — | ✅ |
| `/pharmacy/*` (dispense, inventory) | ✅ | — | — | — | — | ✅ | — |
| `/nurse/*` (vitals, queue) | ✅ | — | — | ✅ | — | — | — |
| `appointments` CRUD + SMS send | ✅ | ✅ | ✅ | — | — | — | — |
| `patients` registry, documents, history | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| `clinical-records` CRUD | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| `chat` (team messaging) | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| `sms` send, logs, templates | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| `profile` | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| `/settings/*` | ✅ | — | — | — | — | — | — |
| `users`, `posts`, `products`, `categories`, `departments`, `clinic-rooms`, `suppliers`, `shifts`, `services` CRUD | ✅ | — | — | — | — | — | — |
| `admin/doctors` management, `admin/queue` | ✅ | — | — | — | — | — | — |
| `invoices` list/show/PDF | ✅ | — | — | — | — | — | — |
| `/reports/*`, `/analytics`, `/activity-logs`, `/notifications` admin panel | ✅ | — | — | — | — | — | — |

> ✅ = allowed · ↩ = redirected to own workspace · — = denied/redirected
>
> **Note:** patients, clinical records, chat, SMS and profile are shared pages — any authenticated staff role can reach them. Reception owns invoice **pay/unpay** actions even though the invoice *viewer/PDF* screens are admin-only.

---

## 5. Patient Visit Workflow (State Machine)

A visit is a strict **state machine** — the heart of the system. Every transition must be validated against the rules below, applied atomically, and written to a per-visit status log (from → to, who, when, optional note). Completing a visit records its completion timestamp.

```
                    ┌────────────┐
                    │ REGISTERED │  reception creates visit
                    └─────┬──────┘
              ┌───────────┼────────────────┐
              ▼           ▼                ▼
    ┌──────────────────┐  │          ┌───────────┐
    │ WAITING_FOR_     │  │          │ CANCELLED │
    │    DOCTOR        │  │          └───────────┘
    └────────┬─────────┘  │
             ▼            │
       ┌───────────┐      │
       │ WITH_     │◄─────┼────── lab / ultrasound results return here
       │ DOCTOR    │      │
       └─────┬─────┘      │
   ┌─────────┼──────────┬─┴───────┐
   ▼         ▼          ▼         ▼
┌───────┐┌─────────┐┌────────┐┌───────────┐
│WAITING││ WAITING ││WAITING ││ COMPLETED │
│FOR_LAB││FOR_US   ││FOR_PHAR││           │
└───┬───┘└────┬────┘└───┬────┘└─────▲─────┘
    ▼         ▼         ▼           │
┌───────┐┌─────────┐┌────────┐      │
│IN_LAB ││ IN_US   ││IN_PHAR │      │
└───┬───┘└────┬────┘└───┬────┘      │
    ▼         ▼         ▼           │
┌───────────┐┌────────────┐┌────────────────┐
│LAB_       ││ULTRASOUND_ ││PHARMACY_       │
│COMPLETED  ││COMPLETED   ││COMPLETED       │
└─────┬─────┘└─────┬──────┘└───┬────────┬───┘
      │            │           │        │
      └──► WITH_ ◄─┘           │        ▼
           DOCTOR              │   ┌──────────────────┐
                               └──►│ WAITING_FOR_     │
                                   │    PAYMENT       │
                                   └────┬──────────▲──┘
                                        ▼          │
                                   ┌──────────────────┐
                                   │    COMPLETED     │ (sets completed_at)
                                   └──────────────────┘
```

### Transition rules (exact)

| From | Allowed To |
|---|---|
| `registered` | `waiting_for_doctor`, `waiting_for_payment`, `cancelled` |
| `waiting_for_doctor` | `with_doctor`, `cancelled` |
| `with_doctor` | `waiting_for_lab`, `waiting_for_ultrasound`, `waiting_for_pharmacy`, `completed`, `cancelled` |
| `waiting_for_lab` | `in_lab` |
| `in_lab` | `lab_completed` |
| `lab_completed` | `with_doctor` |
| `waiting_for_ultrasound` | `in_ultrasound`, `with_doctor` |
| `in_ultrasound` | `ultrasound_completed` |
| `ultrasound_completed` | `with_doctor` |
| `waiting_for_pharmacy` | `in_pharmacy`, `waiting_for_payment` |
| `in_pharmacy` | `pharmacy_completed`, `waiting_for_payment` |
| `pharmacy_completed` | `completed`, `waiting_for_payment` |
| `waiting_for_payment` | `waiting_for_doctor`, `completed` |
| `completed`, `cancelled` | *(terminal — no transitions)* |

Any transition not listed above must be **rejected as an invalid operation** — never allow silent status jumps.

---

## 6. Modules, Pages & Flows

### 6.1 Workspaces (per-role pages)

| Workspace | Pages / Screens | What staff do there |
|---|---|---|
| **Reception** | Dashboard · live queue · payments · call-notifications feed | Register patient → open visit (chief complaint, type) → assign/change doctor → record payment → mark invoice paid/unpaid → close or cancel visit |
| **Doctor** | Queue · dashboard · schedule · patients · lab results · patient history · reports | Call next patient → mark no-show → record vitals → write consultation → order lab tests → order ultrasound → (results return to queue) → prescribe → complete visit |
| **Nurse** | Dashboard · queue · patients | View today's queue → record vitals on a visit → browse patients |
| **Lab** | Queue · order results screen · history · per-patient history | Start processing an order → submit results (whole order or per test item) → visit returns to doctor |
| **Ultrasound** | Dashboard · queue · results screen | Same order lifecycle as lab → submit scan findings → visit returns to doctor |
| **Pharmacy** | Dashboard · queue · inventory · medication editor · dispense history · reports | Receive prescription in queue → dispense items → stock decrements → push visit to payment |

### 6.2 Shared modules (all staff)

| Module | Pages / Screens | Function |
|---|---|---|
| **Appointments** | List · create/edit · per-appointment SMS · bulk-SMS | Schedule appointments; send individual or bulk SMS reminders |
| **Patients** | Registry · live search · patient profile · documents · history timeline | Search/browse patients; upload & download documents; see full visit/record history |
| **Clinical Records** | List · editor · "create from visit" · "create from appointment" | Attachable medical records linked to a visit or appointment |
| **Chat** | Conversation list · chat window · unread badge | Private/team messaging with polling refresh |
| **SMS** | Composer · delivery logs · template manager | Send ad-hoc SMS; view sent/failed log; manage message templates |
| **Profile** | Profile page | View/edit own name, phone, password |

### 6.3 Admin-only modules

| Module | Pages / Screens | Function |
|---|---|---|
| **Dashboard & Analytics** | Main dashboard · stats endpoint · analytics page | KPIs, charts, operational overview |
| **Global queue** | All-visits view | See every visit; admin can discharge, force-complete lab, force-complete payment |
| **Doctors** | List · create/edit · detail | Manage doctor accounts; reset password; activate/deactivate |
| **Users** | List · editor · bulk actions | Full staff account CRUD; bulk activate/deactivate/delete |
| **Catalogs** | Departments · clinic rooms · shifts · services · lab tests (import + sample file) · ultrasound services · lab equipment (status tracking) · products · categories · suppliers · blog posts | All reference-data management |
| **Billing** | Invoice list · invoice detail · PDF download (single + bulk) | View all invoices, export PDFs |
| **Reports** | Sales · patients · doctor performance · stock · revenue · system health | Operational reporting suite |
| **Activity logs** | Log viewer | System-wide audit trail |
| **Notifications** | Compose · templates | Send notifications; manage templates |
| **Settings** | General · Email · SMS (with test-send) · Payment | All system configuration (see §9–10) |

### 6.4 Public site

Home · About · Branches · Services · Blog · Shop · Contact form · **Book Appointment** form (creates an appointment request a receptionist later confirms).

### 6.5 Installer

First-run wizard: welcome → database configuration → run setup → completion screen (see §11).

### 6.6 Typical end-to-end flows

- **Walk-in visit** — Reception registers patient → opens visit → assigns doctor → nurse/doctor records vitals → doctor consults → optional lab/ultrasound loop → prescription → pharmacy dispenses → visit to payment → reception confirms payment → visit completed.
- **Lab loop** — Doctor orders tests (visit → *waiting for lab*) → lab starts (*in lab*) → results submitted (*lab completed*) → visit auto-returns to *with doctor*.
- **Ultrasound loop** — identical to lab, through *waiting for ultrasound → in ultrasound → ultrasound completed → with doctor*.
- **Pharmacy loop** — Doctor prescribes (*waiting for pharmacy*) → pharmacy dispenses (*in pharmacy → pharmacy completed*) → visit goes to *waiting for payment* or *completed*.
- **Payment** — Visit reaches *waiting for payment* → reception records payment / marks invoice paid → visit *completed*. A *waiting for payment* visit can also return to *waiting for doctor* (e.g., pay-first-then-see-doctor flows).
- **Appointment** — Patient books online or reception books → SMS reminder sent → visit created on arrival.

---

## 7. Data Entities

```
Patient ──┬──< Visit ──┬──< LabOrder ──< LabOrderItem ── LabResult / LabAttachment
  │       │            ├──< UltrasoundOrder ──< UltrasoundOrderItem ── UltrasoundAttachment
  │       │            ├──1 Vital
  │       │            ├──1 Consultation
  │       │            ├──< Prescription ──< PrescriptionItem ──< Dispense
  │       │            ├──1 ClinicalRecord ──< ClinicalRecordAttachment
  │       │            ├──1 Invoice ──< InvoiceItem ─── Payment
  │       │            └──< VisitStatusLog
  │       ├──< Appointment
  │       ├──< PatientDocument
  │       └──< ClinicalRecord

User ──┬── (one role: admin | reception | doctor | lab | pharmacy | nurse | ultrasound)
       ├──< Visit (as attending doctor / as registering receptionist)
       ├──< Appointment (as doctor)
       ├──> Department
       └──┬──< Conversation ──< Message   (team chat; membership tracks last-read time)

Catalogs:  LabTest · UltrasoundService · Service · Medication
Inventory: Product ──> Category · Supplier
Org:       Department · ClinicRoom · Shift
System:    Setting · SmsTemplate · SmsLog · EmailTemplate · ActivityLog · Post
```

### Key entity fields

| Entity | Important fields |
|---|---|
| **Patient** | `mrn` (unique medical record number), first/last name, DOB, gender, phone, national ID, address, allergies, blood group — **soft-deleted** |
| **Visit** | `visit_number` (unique), patient, attending doctor, receptionist who registered, `status` (§5), chief complaint, type, registered/completed timestamps |
| **LabOrder / UltrasoundOrder** | visit, ordering doctor, status; items carry test/service + individual results |
| **Prescription** | visit, doctor; items carry medication, dosage, frequency, duration; `Dispense` records pharmacist + quantity given |
| **Invoice** | visit, number, status (paid/unpaid); `InvoiceItem` lines; `Payment` records amount/method/receipt |
| **Appointment** | patient, doctor, `scheduled_at`, type, status, notes |
| **Vital** | visit — BP, temperature, pulse, weight, SpO2, etc. |
| **ClinicalRecord** | patient, visit, doctor — complaint, symptoms, diagnosis, treatment plan, prescription text, notes, record date |
| **Product** | name, SKU, category, cost/selling price, quantity, reorder level, unit, active flag |
| **Setting** | key/value/`group`/`type` — generic system configuration store |
| **SmsTemplate / SmsLog** | template library (name, slug, type, body, active) · delivery log (phone, message, status, gateway, response) |

### Conventions

- **Soft delete** patients — records are recoverable, never hard-destroyed by accident.
- **Passwords are always stored hashed** — the system must hash on save; never persist plaintext.
- **Settings are a generic key/value store** grouped by area: `general`, `email`, `sms`, `payment`.
- Uploaded files (patient documents, lab/ultrasound attachments, record attachments) are served only to logged-in users.

---

## 8. Business Rules

- **Visit numbers** — format `VIS-{YYYY}-{seq}`, e.g. `VIS-2026-000100`.
- **MRN** — format `MRN-{YYYY}-{seq}`, e.g. `MRN-2026-00100`.
- **Visit lifecycle** — only the transitions in §5 are legal; every change is audit-logged with the acting user.
- **Consultation fee** — configurable setting, default `10,000 TSh`.
- **Currency** — `TSh` (Tanzanian Shilling), configurable in settings.
- **Payment flow** — pharmacy can push a visit to *waiting for payment*; reception records payments and marks invoices paid/unpaid; a paid visit can then be *completed*.
- **Order loops** — ordering lab moves the visit to *waiting for lab*; submitted results return it to *with doctor*. Identical for ultrasound.
- **Inventory** — products track SKU, cost & selling price, quantity, reorder level and an active flag.
- **User accounts** — have an active/inactive flag; admin can deactivate instead of deleting, reset passwords, and run bulk activate/deactivate/delete.
- **Appointments** — statuses: `scheduled`, `confirmed`, `completed`, `cancelled`, `no_show`; types: `general`, `followup`, `emergency`.
- **Roles** — exactly seven roles; every staff account has exactly one; admin is implicitly all-powerful.

---

## 9. SMS & Notifications System

Every SMS send is recorded in a delivery log (`pending` → `sent`/`failed` with the full gateway response). The active gateway is chosen by a `sms_gateway` setting.

### Supported gateways

| Gateway | Config keys | Notes |
|---|---|---|
| `log` | — | Testing mode — records the message without actually sending. |
| `nextsms` | `nextsms_username`, `nextsms_password`, `nextsms_from`, `nextsms_url` | Tanzanian gateway (messaging-service.co.tz), Basic-auth JSON POST — **currently in use**. |
| `twilio` | `twilio_sid`, `twilio_token`, `twilio_from` | Standard Twilio Messages API. |
| `http` | `sms_http_url`, `sms_http_method` | Generic GET/POST webhook for any provider. |

- **Phone normalization** — strips non-numerics, converts `0xxx…` → `+255xxx…` (Tanzania); NextSMS gets the `255…` form without `+`.
- **Sender ID** — `sms_sender_id` (default `UZAZICLINIC`).
- **Templates** — an SMS template library ships with 40+ Swahili/English templates grouped by type: registration, appointment, visit, doctor, lab, pharmacy, payment, birthday, holiday, general, reminder, marketing. Placeholders: `{{name}}`, `{{mrn}}`, `{{date}}`, `{{time}}`, `{{amount}}`, `{{phone}}`, `{{clinic}}`, …
- **Bulk send** — bulk appointment reminders from the appointments screen.
- **Settings UI** — SMS settings page includes a live **test SMS** action.
- **Email** — log-only by default; an email-template library exists plus a password-reset email. Real SMTP is configurable on the email settings page.

### Settings groups

`general` (clinic name/phone/email/address/currency/fee) · `email` (mail host/from/encryption) · `sms` (gateways above) · `payment` (gateway, keys, merchant, bank details — default gateway `cash`).

---

## 10. Default Data & Credentials

### 10.1 Default staff accounts

Six staff accounts are created on first run — all share password **`password`** (stored hashed):

| Role | Email | Password |
|---|---|---|
| Admin | `admin@drissa.test` | `password` |
| Reception | `reception@drissa.test` | `password` |
| Doctor | `doctor@drissa.test` | `password` |
| Lab | `lab@drissa.test` | `password` |
| Pharmacy | `pharmacy@drissa.test` | `password` |
| Ultrasound | `ultrasound@drissa.test` | `password` |

> The `nurse` role exists but has **no default account** — admin creates nurse users manually.
> All default passwords must be changeable; production deployments should force a change on first login.

### 10.2 SMS Gateway Credentials (stored in system settings)

Editable on the SMS settings page (admin only):

| Gateway | Key | Value |
|---|---|---|
| **NextSMS** (active gateway) | `nextsms_username` | `Issa Scientist` |
| | `nextsms_password` | `muhas3456` |
| | `nextsms_from` / `sms_sender_id` | `UZAZICLINIC` |
| | `nextsms_url` | `https://messaging-service.co.tz/api/sms/v1/text/single` |
| **Twilio** | `twilio_sid` | *(empty — not configured)* |
| | `twilio_token` | *(empty)* |
| | `twilio_from` | *(empty)* |
| **Generic HTTP** | `sms_http_url` | *(empty)* |
| | `sms_http_method` | `POST` |

### 10.3 Email / SMTP Credentials

Stored in settings (group `email`), editable on the email settings page; mail is log-only until SMTP is configured:

| Key | Value |
|---|---|
| `mail_from` | `noreply@drissa.test` |
| `mail_host` | `smtp.mailtrap.io` |
| `mail_port` | `2525` |
| `mail_username` | *(empty — needs real credentials)* |
| `mail_password` | *(empty)* |
| `mail_encryption` | `tls` |

### 10.4 Payment Gateway Credentials

Stored in settings (group `payment`), editable on the payment settings page:

| Key | Value |
|---|---|
| `payment_gateway` | `cash` (default — no online gateway) |
| `payment_api_key` / `payment_api_secret` / `payment_merchant_id` | *(empty)* |
| `payment_callback_url` | *(empty)* |
| `payment_bank_name` / `payment_bank_account` | *(empty)* |

### 10.5 Other configuration

| Item | Notes |
|---|---|
| **Application secret key** | Generated at install — keep secret, never commit to source control |
| **File storage** | Patient documents, lab/ultrasound attachments, invoice PDFs — served to authenticated users only |
| **Queue/jobs** | Used for notifications; in-process fallback is acceptable |

> ⚠️ **Security note:** the NextSMS password `muhas3456` is stored in plaintext in settings and shipped inside default data — rotate it before production and keep real secrets in environment configuration, not in shipped default data.

### 10.6 Initial dataset

First run also loads: **8 departments** (Administration, Reception, Outpatient, Laboratory, Pharmacy, Radiology, Nursing, Finance) · **3 shifts** (morning 07–15, afternoon 15–23, night 23–07) · **5 clinic rooms** · **8 product categories** · **3 suppliers** · **10 products** · **6 services** · **6 lab tests** (CBC, Malaria, Urinalysis, Blood Sugar, HIV, X-Ray) · **5 medications** · **6 lab equipment items** · **~30 system settings** · **40+ SMS templates** · **4 demo patients with visits** · **14 demo appointments** · **1 clinical record** · **1 demo chat conversation**.

---

## 11. First-Run Setup

- The system ships with a **web installer** (`/install`): welcome screen → database configuration → run setup → completion screen.
- Until installation completes, **all traffic is redirected to the installer**. Once complete, an *installed* flag is stored and the installer pages lock (visiting them redirects to the site).
- Setup generates the application secret key, prepares the database schema, and loads the entire §10 default dataset (roles, admin account, catalogs, settings, SMS templates, demo data).
- Required capabilities at runtime: a relational database, writable file storage for uploads, optional background queue for notifications, and PDF rendering for invoices.
- Sensible defaults: embedded file-based database for single-machine deployment; upgradeable to a server database for multi-user clinics.

---

## 12. Security & Audit Requirements

- **Authentication required** for every staff page; only the public site, login and installer are open.
- **Self-registration disabled** — only admin creates staff accounts.
- **Role isolation** — staff reaching another department's page are softly redirected to their own workspace; admin areas hard-deny non-admins.
- **Protected files** — uploaded documents/attachments are served only to logged-in users.
- **Passwords** — always hashed; admin can reset a staff password without seeing the old one.
- **Soft deletes** — patient records are never hard-deleted.
- **Audit trail** — a global activity log (admin viewer) plus a per-visit status log recording every transition and the acting user.
- **Install lock** — the installer cannot be re-run after completion.
- **Secrets management** — gateway passwords live in admin-editable settings; production secrets belong in environment config, not default data.
- **Session scope** — staff sessions expire after a configurable lifetime (default 120 minutes).

---

*Domain specification for the Uzazi Clinic system — the source of truth for rebuilding it on any technology stack.*
