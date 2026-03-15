import { ArrowUpRight } from "lucide-react";
import { Button } from "../../../components/ui/button";

const stats = [
  { title: "Total Employees", value: "248" },
  { title: "Employees Present Today", value: "221" },
  { title: "Employees Late", value: "14" },
  { title: "Employees Absent", value: "13" },
];

const recentAttendance = [
  { employee: "Ayu Saputri", checkIn: "08:02", checkOut: "17:05", status: "Present", location: "Head Office" },
  { employee: "Dimas Nugraha", checkIn: "08:20", checkOut: "17:10", status: "Late", location: "Branch A" },
  { employee: "Nabila Putri", checkIn: "08:00", checkOut: "17:01", status: "Present", location: "Head Office" },
];

export function DashboardPage() {
  return (
    <section className="space-y-6">
      <header>
        <h1 className="text-2xl font-semibold">Dashboard</h1>
        <p className="text-sm text-muted-foreground">Ringkasan sistem absensi.</p>
      </header>

      <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        {stats.map((item) => (
          <article key={item.title} className="rounded-2xl border border-border bg-card p-5">
            <p className="text-sm text-muted-foreground">{item.title}</p>
            <h2 className="mt-2 text-4xl font-semibold tracking-tight">{item.value}</h2>
          </article>
        ))}
      </div>

      <div className="grid gap-6 xl:grid-cols-2">
        <section className="rounded-2xl border border-border bg-card p-5">
          <h3 className="text-lg font-semibold">Attendance Chart (7 days)</h3>
          <div className="chart-shell relative mt-4 h-[250px] overflow-hidden rounded-xl border border-border/70">
            <div className="chart-wave chart-wave-1" />
            <div className="chart-wave chart-wave-2" />
          </div>
        </section>

        <section className="rounded-2xl border border-border bg-card p-5">
          <h3 className="text-lg font-semibold">Attendance Chart (Monthly)</h3>
          <div className="chart-shell relative mt-4 h-[250px] overflow-hidden rounded-xl border border-border/70">
            <div className="chart-wave chart-wave-1" />
            <div className="chart-wave chart-wave-2" />
          </div>
        </section>
      </div>

      <section className="rounded-2xl border border-border bg-card p-5">
        <h3 className="mb-4 text-lg font-semibold">Recent Attendance</h3>
        <div className="overflow-x-auto rounded-xl border border-border">
          <table className="w-full border-collapse text-sm">
            <thead className="bg-muted/40 text-left text-xs uppercase tracking-wide text-muted-foreground">
              <tr>
                <th className="px-4 py-3">Employee</th>
                <th className="px-4 py-3">Check In Time</th>
                <th className="px-4 py-3">Check Out Time</th>
                <th className="px-4 py-3">Status</th>
                <th className="px-4 py-3">Location</th>
              </tr>
            </thead>
            <tbody>
              {recentAttendance.map((row) => (
                <tr key={row.employee} className="border-t border-border">
                  <td className="px-4 py-3 font-medium">{row.employee}</td>
                  <td className="px-4 py-3">{row.checkIn}</td>
                  <td className="px-4 py-3">{row.checkOut}</td>
                  <td className="px-4 py-3">{row.status}</td>
                  <td className="px-4 py-3 text-muted-foreground">{row.location}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </section>

      <section className="rounded-2xl border border-border bg-card p-5">
        <h3 className="mb-4 text-lg font-semibold">Quick Actions</h3>
        <div className="flex flex-wrap gap-3">
          <Button className="gap-2">Scan Barcode <ArrowUpRight className="size-4" /></Button>
          <Button variant="outline">Generate Barcode</Button>
          <Button variant="outline">Add Employee</Button>
        </div>
      </section>
    </section>
  );
}
