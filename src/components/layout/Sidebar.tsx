import { Activity, Barcode, BriefcaseBusiness, Home, KeyRound, ShieldCheck, Users } from "lucide-react";
import { NavLink } from "react-router-dom";
import { useAuth } from "../../app/providers/AuthProvider";
import { APP_ROUTES, ROUTE_ACCESS } from "../../lib/constants";
import { cn } from "../../lib/utils";

const menuItems = [
  { name: "Dashboard", icon: Home, to: APP_ROUTES.home, access: ROUTE_ACCESS.home },
  { name: "Users", icon: Users, to: APP_ROUTES.users, access: ROUTE_ACCESS.users },
  { name: "Employees", icon: BriefcaseBusiness, to: APP_ROUTES.employees, access: ROUTE_ACCESS.employees },
  { name: "Barcodes", icon: Barcode, to: APP_ROUTES.barcodes, access: ROUTE_ACCESS.barcodes },
  { name: "Attendance", icon: Activity, to: APP_ROUTES.attendance, access: ROUTE_ACCESS.attendance },
  { name: "Activity Logs", icon: Activity, to: APP_ROUTES.activityLogs, access: ROUTE_ACCESS.activityLogs },
  { name: "Roles", icon: ShieldCheck, to: APP_ROUTES.roles, access: ROUTE_ACCESS.roles },
  { name: "Permissions", icon: KeyRound, to: APP_ROUTES.permissions, access: ROUTE_ACCESS.permissions },
];

export function Sidebar() {
  const { user } = useAuth();

  const visibleItems = menuItems.filter((item) => {
    if (!user) {
      return false;
    }

    const hasRole = item.access.allowedRoles.some((role) => user.roles.includes(role));
    const requiredPermissions = "requiredPermissions" in item.access ? item.access.requiredPermissions : [];
    const hasPermission = requiredPermissions.length === 0 || requiredPermissions.some((permission) => user.permissions.includes(permission));

    return hasRole || hasPermission;
  });

  return (
    <aside className="hidden w-72 border-r border-border/70 bg-sidebar lg:block">
      <div className="border-b border-border/70 px-6 py-5">
        <h1 className="text-lg font-semibold text-sidebar-foreground">Acme Inc.</h1>
      </div>
      <nav className="space-y-1 p-4">
        {visibleItems.map((item) => (
          <NavLink
            key={item.name}
            to={item.to}
            className={({ isActive }) =>
              cn(
                "flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-muted-foreground transition-colors",
                isActive && "bg-secondary text-foreground",
              )
            }
          >
            <item.icon className="size-4" />
            <span>{item.name}</span>
          </NavLink>
        ))}
      </nav>
    </aside>
  );
}
