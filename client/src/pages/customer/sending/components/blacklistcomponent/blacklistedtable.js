import { useEffect } from "react";
import { useDispatch, useSelector } from "react-redux";
import Spinner from "../../../../../components/spinner/spinner";
import { DeleteBlacklist, GetBlacklist } from "../../../../../store/BlacklistedSlice";
import { Actions } from "./blacklistaction";
import { NoData } from "../../../../../components/nodata";
import { DeleteBtn } from "../../../template/components/deleteBtn";
import { ViewportList } from "react-viewport-list";
import { useRef } from "react";

export const BlacklistTable =()=>{
    const listRef = useRef(null)
    const blacklist = useSelector(
        state => state.blacklist
    )
    const dispatch = useDispatch();
    useEffect(()=>{
        dispatch(GetBlacklist(null));
    },[dispatch])

    if(blacklist.GetBlacklistStatus ==='pending'){
        return <Spinner/>
    }
    return(
        <>
        <Actions/>
        <div className="w-overflow" ref={listRef}>
            <table className=" table table-striped table-hover table-bordered table-responsive caption-top mb-3">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Email</th>
                        <th scope="col">Created At</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <ViewportList viewportRef={listRef} items={ blacklist.blacklist} margin={8}>
                    {
                        ((list,index)=>{
                                const{
                                    email,
                                    id,
                                    created_at
                                } = list
                                return(
                                    <tr key={index}>
                                        <th scope="row">{index}</th>
                                        <td>{email}</td>
                                        <td>{
                                                new Date(created_at)
                                                .toLocaleString()
                                            }
                                        </td>
                                        <td>
                                            <div className="d-flex align-items-center">
                                                <div className="dropdown">
                                                    <button 
                                                        className="btn btn-secondary dropdown-toggle" 
                                                        type="button"
                                                        data-bs-toggle="dropdown" 
                                                        aria-expanded="false" 
                                                    >
                                                    </button>
                                                    <ul className="dropdown-menu">
                                                        <DeleteBtn
                                                            status = {blacklist.deleteStatus}
                                                            deleteFunc ={DeleteBlacklist({id})}
                                                        />
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                )
                            }
                        )
                    }
                    </ViewportList>
                </tbody>
            </table>
        </div>
       
        {
           blacklist.blacklist?.length === 0 && <NoData/>
        }
        </>
    )
}