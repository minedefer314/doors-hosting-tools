import { BrowserRouter, Routes, Route } from "react-router-dom"
import './App.css'
import Navbar from "./navbar"
import Home from "./pages/Home";
import Games from "./pages/Games"

function App() {
  return (
        <BrowserRouter>
            <Routes>
                <Route path="/" element={<Navbar />}>
                    <Route index element={<Home />} />
                    <Route path="games" element={<Games />} />
                    <Route path="*" element={<Home />} />
                </Route>
            </Routes>
        </BrowserRouter>
  )
}

export default App
