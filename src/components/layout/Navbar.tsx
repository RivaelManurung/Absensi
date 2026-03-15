import { Moon, Plus, Sun } from "lucide-react";
import { useTheme } from "../../app/providers/ThemeProvider";
import { useAuth } from "../../app/providers/AuthProvider";
import { Button } from "../ui/button";

export function Navbar() {
  const { theme, toggleTheme } = useTheme();
  const { user, logout } = useAuth();

  return (
    <header className="flex items-center justify-between border-b border-border/70 bg-card/60 px-4 py-4 backdrop-blur md:px-8">
      <div>
        <p className="text-xs uppercase tracking-widest text-muted-foreground">Documents</p>
        <h2 className="text-lg font-semibold text-foreground">Dashboard</h2>
      </div>
      <div className="flex items-center gap-2">
        <p className="hidden text-sm text-muted-foreground md:block">{user?.name ?? "Guest"}</p>
        <Button variant="outline" size="sm" onClick={toggleTheme}>
          {theme === "dark" ? <Sun className="mr-2 size-4" /> : <Moon className="mr-2 size-4" />}
          {theme === "dark" ? "Light" : "Dark"}
        </Button>
        <Button size="sm" className="gap-2" onClick={() => void logout()}>
          <Plus className="size-4" />
          Logout
        </Button>
      </div>
    </header>
  );
}
