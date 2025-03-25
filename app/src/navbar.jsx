import {Outlet, useLocation} from "react-router-dom"
import React, {useEffect, useState} from "react";

const Navbar = () => {
    const [loginData, setLoginData] = useState({
    })
    const getLoginData = async () => {
        let myHeaders = new Headers({'Content-Type': 'application/json'});
        const response = await fetch("/api/login-data")
        setLoginData(await response.json())
    }

    useEffect(() => {
        getLoginData()
    }, [])

    const playerInfo = () => {
        if(loginData["logged"]) {
            return (
                <>
                    <a
                        href="/account"
                        className="d-flex align-items-center text-decoration-none"
                    >
                        <p className="m-0 me-2">{loginData["user"]["displayName"]}</p>
                        <img
                            src={loginData["user"]["picture"] ? loginData["user"]["picture"] : ""}
                            alt={loginData["user"]["displayName"] + "'s avatar"}
                            className="rounded-circle border border-secondary"
                            height="40"
                        />
                    </a>
                    <a
                        className="btn btn-outline-danger btn-sm"
                        href="/logout"
                    >Log out</a>
                </>
            )
        } else {
            return (
                <a
                    className="btn btn-outline-primary btn-sm"
                    href="/login"
                >Log in</a>
            )
        }
    }

    const menu = () => {
        const isActive = (route) => {
            const location = useLocation();
            const { hash, pathname, search } = location;
            if(pathname === route) {
                return "nav-link active"
            } else {
                return "nav-link"
            }
        }
        const displayNavItem = (name, route) => {
            return (
                <li className="nav-item">
                    <a
                        className={isActive(route)}
                        aria-current="page"
                        href={route}
                    >{name}</a>
                </li>
            )
        }
        return (
            <>
                {displayNavItem("Games", "/games")}
                {displayNavItem("Players", "#")}
                {displayNavItem("About us", "#")}
            </>
        )
    }

    return (
        <>
            <nav className="navbar navbar-expand-lg navbar-light bg-body-tertiary shadow-sm mb-5 sticky-top">
                <div className="container-fluid">
                    <a
                        className="navbar-brand"
                        href="/"
                    >Doors Hosting</a>

                    <button
                        className="navbar-toggler" type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent"
                        aria-expanded="false"
                        aria-label="Toggle navigation"
                    >
                        <span className="navbar-toggler-icon"></span>
                    </button>

                    <div
                        className="collapse navbar-collapse justify-content-between"
                        id="navbarSupportedContent"
                    >
                        <ul className="navbar-nav me-auto mb-2 mb-lg-0 nav-underline">
                            {menu()}
                        </ul>

                        <div className="d-flex flex-row align-items-center gap-2 flex-wrap">
                            {playerInfo()}
                        </div>
                    </div>
                </div>
            </nav>
            <Outlet />
        </>
    )
}
export default Navbar