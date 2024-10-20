import {Link} from "@inertiajs/react";

export default function Layout({children}) {
  return (
    <>
      <header>
        <nav>
          <Link className="nav-link" href="/">
            Home
          </Link>
        </nav>
      </header>

      <main>{children}</main>
    </>
  );
}
