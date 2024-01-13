  <div class="header">
    <div class="wrapper">

      <ul class="nav">
        <li><a id="options-button" class="ui-button ui-corner-all ui-widget" ><span class="ui-icon ui-icon-gear"></span> Options Menu</a></li>

        <li><a id="admin-button" class="ui-button ui-corner-all ui-widget" ><span class="ui-icon ui-icon-gear"></span> Admin Menu</a></li>

      </ul>

    </div>
  </div>

  <div class="menu">
    <div class="wrapper">

      <table id="options-menu">
        <thead>
          <tr>
            <th>Option</th>

            <th>Description</th>

          </tr>
        </thead>
        <tbody>
          <tr>
            <td><a href="slurminfo.php">SLURM Info</a></td>

            <td>SLURM component versions and node information.</td>

          </tr>
          <tr>
            <td><a href="tables.php">SLURM Tables</a></td>

            <td>A list of browseable tables in the slurm_acct_db database.</td>

          </tr>
          <tr>
            <td><a href="jobs.php?ty=current">Current Jobs</a></td>

            <td>A list of current jobs submitted to SLURM.</td>

          </tr>
          <tr>
            <td><a href="jobs.php">Completed Jobs</a></td>

            <td>A list of completed jobs submitted to SLURM.</td>

          </tr>
          <tr>
            <td><a href="events.php?ty=current">Current Events</a></td>

            <td>A list of current SLURM events.</td>

          </tr>
          <tr>
            <td><a href="events.php">Completed Events</a></td>

            <td>A list of completed SLURM events.</td>

          </tr>
          <tr>
            <td><a href="transactions.php">Transactions</a></td>

            <td>A list of SLURM transactions.</td>

          </tr>
          <tr>
            <td><a href="users.php">Users</a></td>

            <td>A list of users defined in SLURM.</td>

          </tr>
          <tr>
            <td><a href="topusers.php">Top Users</a></td>

            <td>A list of SLURM users by CPU usage.</td>

          </tr>
          <tr>
            <td><a href="metrics.php">Usage Metrics</a></td>

            <td>SLURM hourly usage metrics.</td>

          </tr>
          <tr>
            <td><a href="dbhealth.php">Database Health</a></td>

            <td>Data fragmentation metrics on the SLURM database.</td>

          </tr>
          <tr>
            <td><a href="dbvars.php">Database Settings</a></td>

            <td>Global variables for all MariaDB databases.</td>

          </tr>
        </tbody>
      </table>

    </div>
  </div>
