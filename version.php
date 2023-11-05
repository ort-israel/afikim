<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
<<<<<<< Updated upstream
// You should have received a copy of the GNU General Public Licensáe
=======
// You should have received a copy of the GNU General Public License
>>>>>>> Stashed changes
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * MOODLE VERSION INFORMATION
 *
 * This file defines the current version of the core Moodle code being used.
 * This is compared against the values stored in the database to determine
 * whether upgrades should be performed (see lib/db/*.php)
 *
 * @package    core
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

<<<<<<< Updated upstream
$version  = 2020061519.03;              // 20200615      = branching date YYYYMMDD - do not modify!
                                        //         RR    = release increments - 00 in DEV branches.
                                        //           .XX = incremental changes.
$release  = '3.9.19+ (Build: 20230203)'; // Human-friendly version name
$branch   = '39';                       // This version's branch.
=======
$version  = 2021051716.00;              // 20210517      = branching date YYYYMMDD - do not modify!
                                        //         RR    = release increments - 00 in DEV branches.
                                        //           .XX = incremental changes.
$release  = '3.11.16 (Build: 20230814)';// Human-friendly version name
$branch   = '311';                      // This version's branch.
>>>>>>> Stashed changes
$maturity = MATURITY_STABLE;             // This version's maturity level.
