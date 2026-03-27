import { Outlet } from 'react-router-dom'
import Sidebar from './Sidebar'
import TopBar from './TopBar'
import { ToastContainer } from '../ui/Toast'

export default function Layout() {
  return (
    <div className="min-h-screen bg-gray-50">
      <Sidebar />
      <div className="content-offset min-h-screen flex flex-col">
        <TopBar />
        <main className="flex-1 p-6">
          <Outlet />
        </main>
      </div>
      <ToastContainer />
    </div>
  )
}
