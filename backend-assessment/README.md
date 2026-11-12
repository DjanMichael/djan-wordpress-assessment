# Back-end Assessment: Jobs Board theme

A "Jobs" custom post type with Job Title, Salary and Location fields, built without plugins.

Theme: `wp-content/themes/jobs-board`

![Theme screenshot](wp-content/themes/jobs-board/screenshot.jpg)

## Requirements

| Requirement | How it is met |
| --- | --- |
| Custom post type "Jobs": add, edit, delete | `register_post_type( 'job' )` in `inc/jobs.php`, with its own **Jobs** admin menu. |
| Job Title, Salary and Location fields | Registered with `register_post_meta()` and edited in a **Job details** box under the job name. |
| No plugins | Core WordPress APIs only. |
| Archive view with all fields | `archive-job.php`, at `/jobs/`. |
| Single view with all fields | `single-job.php`, at `/jobs/{slug}/`. |
| Git repository | This repository. WordPress core is not committed. |
